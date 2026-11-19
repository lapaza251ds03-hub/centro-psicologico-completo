<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\EvaluacionClinica;
use App\Models\Paciente;
use App\Models\Psicologo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuarios institucionales (Contraseña: password)
        $admin = User::create([
            'dni' => '70000001',
            'name' => 'Luis Ruben Apaza (Director)',
            'email' => 'admin@centropsicologico.pe',
            'password' => Hash::make('password'),
            'rol' => 'admin',
            'estado' => 'activo',
        ]);

        $userPsicologa = User::create([
            'dni' => '70000002',
            'name' => 'Dra. Valeria Mendoza',
            'email' => 'psicologo@centropsicologico.pe',
            'password' => Hash::make('password'),
            'rol' => 'psicologo',
            'estado' => 'activo',
        ]);

        User::create([
            'dni' => '70000003',
            'name' => 'Camila Torres (Recepción)',
            'email' => 'recepcion@centropsicologico.pe',
            'password' => Hash::make('password'),
            'rol' => 'recepcionista',
            'estado' => 'activo',
        ]);

        // Usuario con solicitud pendiente para probar RF02
        User::create([
            'dni' => '70000004',
            'name' => 'Dr. Mateo Salazar (Solicitante)',
            'email' => 'pendiente@centropsicologico.pe',
            'password' => Hash::make('password'),
            'rol' => 'psicologo',
            'estado' => 'pendiente',
        ]);

        // 2. Equipo Terapéutico (cubriendo los 4 grupos etarios de RN05)
        $psicoAdultos = Psicologo::create([
            'user_id' => $userPsicologa->id,
            'dni' => '70000002',
            'nombres' => 'Valeria',
            'apellidos' => 'Mendoza Paredes',
            'especialidad' => 'Clínica',
            'grupo_etario' => 'adultos',
            'colegiatura' => 'C.Ps.P. 24810',
            'activo' => true,
        ]);

        $psicoNinos = Psicologo::create([
            'dni' => '71234561',
            'nombres' => 'Diego',
            'apellidos' => 'Quispe Huamán',
            'especialidad' => 'Infantil',
            'grupo_etario' => 'ninos',
            'colegiatura' => 'C.Ps.P. 31904',
            'activo' => true,
        ]);

        $psicoAdolescentes = Psicologo::create([
            'dni' => '71234562',
            'nombres' => 'Sofía',
            'apellidos' => 'Cárdenas Rojas',
            'especialidad' => 'Educativa',
            'grupo_etario' => 'adolescentes',
            'colegiatura' => 'C.Ps.P. 28512',
            'activo' => true,
        ]);

        // Psicólogo inactivo/vacaciones para probar RN04
        Psicologo::create([
            'dni' => '71234563',
            'nombres' => 'Héctor',
            'apellidos' => 'Vargas Luna',
            'especialidad' => 'Neuropsicología',
            'grupo_etario' => 'adulto_mayor',
            'colegiatura' => 'C.Ps.P. 19402',
            'activo' => false,
        ]);

        // 3. Pacientes de prueba (Adulto + Menor con Apoderado)
        $pacienteAdulto = Paciente::create([
            'dni' => '45891234',
            'nombres' => 'Carlos Alberto',
            'apellidos' => 'Mamani Flores',
            'fecha_nacimiento' => '1994-06-15',
            'sexo' => 'M',
            'telefono' => '984112233',
            'motivo_consulta' => 'Ansiedad laboral e insomnio recurrente.',
            'consentimiento_informado' => true,
            'activo' => true,
        ]);

        $pacienteMenor = Paciente::create([
            'dni' => '81234567',
            'nombres' => 'Luciana',
            'apellidos' => 'Condori Pérez',
            'fecha_nacimiento' => '2017-03-20',
            'sexo' => 'F',
            'telefono' => '984998877',
            'motivo_consulta' => 'Dificultades de atención y adaptación escolar.',
            'apoderado_nombre' => 'María Elena Pérez Soto',
            'apoderado_dni' => '41234567',
            'consentimiento_informado' => true,
            'activo' => true,
        ]);

        // 4. Citas en bloques de 60 minutos (RN06)
        $inicioPasada = now()->subDays(2)->setTime(9, 0);
        $citaAtendida = Cita::create([
            'paciente_id' => $pacienteAdulto->id,
            'psicologo_id' => $psicoAdultos->id,
            'fecha_hora' => $inicioPasada,
            'fecha_hora_fin' => $inicioPasada->copy()->addMinutes(60),
            'modalidad' => 'presencial',
            'tipo_sesion' => 'Evaluación inicial',
            'estado' => 'atendida',
        ]);

        EvaluacionClinica::create([
            'cita_id' => $citaAtendida->id,
            'diagnostico_presuntivo' => 'Trastorno de ansiedad generalizada leve (F41.1)',
            'evolucion_terapeutica' => 'Paciente colaborativo durante la entrevista clínica. Se establecen objetivos terapéuticos cognitivo-conductuales.',
            'pruebas_aplicadas' => 'Inventario de Ansiedad de Beck (BAI)',
            'observaciones_confidenciales' => 'Recomendar higiene del sueño y seguimiento semanal.',
        ]);

        $inicioHoy = now()->setTime(10, 0);
        Cita::create([
            'paciente_id' => $pacienteMenor->id,
            'psicologo_id' => $psicoNinos->id,
            'fecha_hora' => $inicioHoy,
            'fecha_hora_fin' => $inicioHoy->copy()->addMinutes(60),
            'modalidad' => 'presencial',
            'tipo_sesion' => 'Terapia infantil',
            'estado' => 'confirmada',
        ]);
    }
}
