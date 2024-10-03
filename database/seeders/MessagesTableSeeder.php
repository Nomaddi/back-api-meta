<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessagesTableSeeder extends Seeder
{
    public function run()
    {
        $records = [];
        $initialDate = Carbon::create(2024, 5, 30, 7, 57, 11); // Fecha inicial
        $minDate = Carbon::create(1970, 1, 1, 0, 0, 0); // Fecha mínima permitida

        for ($i = 0; $i < 1000000; $i++) {
            $wamId = 'wamid.HBgMNTczMTA1MzIwNjU5FQIAEhgWM0VCMDlGN0ZCRDRFQ0RFQTIzMkM3QQA=' . $i;

            // Limitar la fecha para que no baje de 1970
            $date = $initialDate->copy()->subSeconds($i);
            if ($date < $minDate) {
                $date = $minDate;
            }

            $records[] = [
                'wa_id' => '573105320659',
                'wam_id' => $wamId,
                'phone_id' => '131481643386780',
                'type' => 'template',
                'outgoing' => true,
                'body' => 'Welcome and congratulations!! This message demonstrates your ability to send a WhatsApp message notification from the Cloud API, hosted by Meta. Thank you for taking the time to test with us.',
                'status' => 'sent',
                'caption' => null,
                'data' => '', // Valor no nulo
                'distintivo' => '',
                'code' => '',
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // Inserción en lotes de 1,000 registros para evitar sobrecargar la base de datos
            if ($i % 1000 == 0 && $i > 0) {
                DB::table('messages')->insert($records);
                $records = []; // Limpiar los registros después de cada lote
            }
        }

        // Insertar cualquier registro restante
        if (count($records) > 0) {
            DB::table('messages')->insert($records);
        }
    }
}
