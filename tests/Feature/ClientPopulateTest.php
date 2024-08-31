<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
class ClientPopulateTest extends TestCase
{
    # php artisan test  --filter=ClientPopulateTest::test_main
    public function test_main(): void
    {
        $total_registros = 500;
        $clientes_antes = Cliente::count();
        Cliente::factory($total_registros)->create();
        $clientes_depois = Cliente::count();
        $this->assertEquals(
            $clientes_depois,
            $clientes_antes + $total_registros,
            "Não foi criado a quantidade de clientes esperado"
        );
    }


    # php artisan test  --filter=ClientPopulateTest::test_delete_model
    public function test_delete_model()
    {//Deleta um registro e aplica o soft delete no registro
        $clientes = Cliente::query()->inRandomOrder()->first();
        $cliente_id = $clientes->id;
        $clientes->delete();
        $this->assertSoftDeleted('clientes', ['id' => $cliente_id]);
        //Verifica o id do cliente deletado
        $clientes = Cliente::withTrashed()->find($cliente_id);
        //Verifica o id do cliente deletado
        $this->assertTrue($clientes->trashed());
        dump("Id do cliente deletado ".$cliente_id);
    }

    # php artisan test  --filter=ClientPopulateTest::test_restore_deleted_model
    //Restaurar entidade que foi deletada
    public function test_restore_deleted_model(){
        $clientes_deleted = Cliente::withTrashed()->where('deleted_at', '<>', null)->first();
        $clientes_deleted->restore();
        //Para verificar si o cliente esta recuperado
        $this->assertFalse($clientes_deleted->trashed());

//Para restaurar um cliente deletado
        // $this->assertSoftDeleted('clientes', ['id' => $clientes_deleted->id]);
        // dd($clientes_deleted);
        // //Verifica o id do cliente deletado
        // $clientes = Cliente::withTrashed()->find($cliente_id);
    }
    # php artisan test  --filter=ClientPopulateTest::test_remove_deleted_model
public function test_remove_deleted_model(){
    $clientes_deleted = Cliente::onlyTrashed()->first();
    //Traz quem está deletado
    //$clientes_deleted = Cliente::withTrashed()->where('deleted_at', '<>', null)->first();
    $clientes_deleted->deleted_at = null;
    $clientes_deleted->idade +=100;
    $clientes_deleted->save();
    dump($clientes_deleted->id);
    //Para verificar si o cliente esta recuperado
    $this->assertFalse($clientes_deleted->trashed());
}
# php artisan test  --filter=ClientPopulateTest::test_force_deleted_model
public function test_force_deleted_model(){
$clientes_deleted = Cliente::onlyTrashed()->first();
$cliente_id = $clientes_deleted->id;
dump($clientes_deleted->id);
$clientes_deleted->forceDelete();

$clientes_deleted = Cliente::onlyTrashed()->find($cliente_id);
//Para verificar si o cliente esta recuperado
$this->assertNull($clientes_deleted);
}
}
