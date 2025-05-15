<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Models\Categoria;
use App\Models\Producto;

class CategoriaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function categoria_puede_tener_varios_productos()
    {
        // 1) Creamos una categoría con código numérico (primary key unsignedInteger) 
        $categoria = Categoria::factory()->create([
            'codigo'                => 100,
            'descripcion_categoria' => 'Papelería'
        ]); // según migración de categorias :contentReference[oaicite:1]{index=1}

        // 2) Creamos 2 productos que referencien ese código de categoría
        $productos = Producto::factory()
            ->count(2)
            ->create([
                'codigo' => $categoria->codigo,
            ]);

        // 3) Refrescamos la instancia para cargar la relación
        $categoria->refresh();

        // 4) Aserciones: debe haber exactamente 2 productos relacionados
        $this->assertCount(2, $categoria->productos);
        $this->assertTrue($categoria->productos->contains($productos[0]));
        $this->assertTrue($categoria->productos->contains($productos[1]));
    }

    /** @test */
    public function descripcion_categoria_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        // crea una categoría con la columna obligatoria en null
        Categoria::factory()->create([
            'descripcion_categoria' => null,
        ]);
    }

    /** @test */
    public function descripcion_categoria_no_puede_superar_150_caracteres()
    {
        $this->expectException(QueryException::class);

        // genera un string de 151 chars
        Categoria::factory()->create([
            'descripcion_categoria' => Str::repeat('A', 151),
        ]);
    }
}
