<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Psicologo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReglasNegocioPsicologiaTest extends TestCase
{
    use RefreshDatabase;

    public function test_rn01_usuario_pendiente_no_puede_iniciar_sesion(): void
    {
        $pendiente = User::create([
            'dni' => '70000099',
            'name' => 'Solicitante Prueba',
            'email' => 'prueba@centropsicologico.pe',
            'password' => bcrypt('password'),
            'rol' => 'psicologo',
            'estado' => 'pendiente',
        ]);

        $response = $this->post('/login', [
            'email' => $pendiente->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_rn03_menor_de_edad_exige_apoderado_y_consentimiento(): void
    {
        $recepcion = User::create([
            'dni' => '70000088',
            'name' => 'Recepcionista',
            'email' => 'rec@centropsicologico.pe',
            'password' => bcrypt('password'),
            'rol' => 'recepcionista',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($recepcion)->post('/pacientes', [
            'dni' => '89999999',
            'nombres' => 'Mateo',
            'apellidos' => 'Quispe',
            'fecha_nacimiento' => now()->subYears(10)->format('Y-m-d'),
            'sexo' => 'M',
            'motivo_consulta' => 'Evaluación escolar',
        ]);

        $response->assertSessionHasErrors(['apoderado_nombre', 'apoderado_dni', 'consentimiento_informado']);
    }

    public function test_rn05_y_rn07_triaje_etario_y_cruce_de_horario_en_citas(): void
    {
        $admin = User::create([
            'dni' => '70000077',
            'name' => 'Admin',
            'email' => 'adm@centropsicologico.pe',
            'password' => bcrypt('password'),
            'rol' => 'admin',
            'estado' => 'activo',
        ]);

        $psicoAdultos = Psicologo::create([
            'dni' => '71111111',
            'nombres' => 'Ana',
            'apellidos' => 'Pérez',
            'especialidad' => 'Clínica',
            'grupo_etario' => 'adultos',
            'colegiatura' => 'C.Ps.P. 90001',
            'activo' => true,
        ]);

        $pacienteNino = Paciente::create([
            'dni' => '81111111',
            'nombres' => 'Leo',
            'apellidos' => 'Ramos',
            'fecha_nacimiento' => now()->subYears(8)->format('Y-m-d'),
            'sexo' => 'M',
            'motivo_consulta' => 'Atención',
            'apoderado_nombre' => 'Rosa Ramos',
            'apoderado_dni' => '41111111',
            'consentimiento_informado' => true,
        ]);

        // Bloqueo por triaje etario (RN05)
        $resTriaje = $this->actingAs($admin)->post('/citas', [
            'paciente_id' => $pacienteNino->id,
            'psicologo_id' => $psicoAdultos->id,
            'fecha_hora' => now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
            'modalidad' => 'presencial',
            'tipo_sesion' => 'Evaluación',
        ]);
        $resTriaje->assertSessionHasErrors('psicologo_id');

        // Bloqueo por cruce de horario de 60 minutos (RN07)
        $pacienteAdulto1 = Paciente::create([
            'dni' => '42222222',
            'nombres' => 'Juan',
            'apellidos' => 'Díaz',
            'fecha_nacimiento' => '1990-05-10',
            'sexo' => 'M',
            'motivo_consulta' => 'Estrés',
        ]);

        $pacienteAdulto2 = Paciente::create([
            'dni' => '43333333',
            'nombres' => 'Luis',
            'apellidos' => 'Sosa',
            'fecha_nacimiento' => '1988-08-20',
            'sexo' => 'M',
            'motivo_consulta' => 'Ansiedad',
        ]);

        $hora = now()->addDays(2)->setTime(15, 0);
        Cita::create([
            'paciente_id' => $pacienteAdulto1->id,
            'psicologo_id' => $psicoAdultos->id,
            'fecha_hora' => $hora,
            'fecha_hora_fin' => $hora->copy()->addMinutes(60),
            'modalidad' => 'presencial',
            'tipo_sesion' => 'Terapia',
            'estado' => 'confirmada',
        ]);

        $resCruce = $this->actingAs($admin)->post('/citas', [
            'paciente_id' => $pacienteAdulto2->id,
            'psicologo_id' => $psicoAdultos->id,
            'fecha_hora' => $hora->copy()->addMinutes(30)->format('Y-m-d H:i:s'),
            'modalidad' => 'virtual',
            'tipo_sesion' => 'Terapia',
        ]);
        $resCruce->assertSessionHasErrors('fecha_hora');
    }

    public function test_rn09_recepcionista_no_accede_a_historial_clinico(): void
    {
        $recepcion = User::create([
            'dni' => '70000066',
            'name' => 'Recepción',
            'email' => 'r2@centropsicologico.pe',
            'password' => bcrypt('password'),
            'rol' => 'recepcionista',
            'estado' => 'activo',
        ]);

        $paciente = Paciente::create([
            'dni' => '44444444',
            'nombres' => 'Carla',
            'apellidos' => 'Vega',
            'fecha_nacimiento' => '1995-01-01',
            'sexo' => 'F',
            'motivo_consulta' => 'Consulta',
        ]);

        $this->actingAs($recepcion)
            ->get("/evaluaciones/paciente/{$paciente->id}/historial")
            ->assertForbidden();
    }
}