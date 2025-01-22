<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->timestamp("fecha_emision");
            $table->timestamp("fecha_autorizacion");
            $table->string("ruc_emisor");
            $table->string("clave_acceso");
            $table->string("razon_social_emisor");
            $table->string("nombre_comercial_emisor")->nullable();
            $table->string("serie_comprobante");
            $table->text("direccion_matriz");
            $table->text("direccion_establecimiento");
            $table->string("tipo_identificacion_comprador")->nullable();
            $table->string("identificacion_comprador");
            $table->string("contribuyente_especial")->nullable();
            $table->string("obligado_llevar_contabilidad")->nullable();
            $table->string("cod_doc_modificado");
            $table->string("num_doc_modificado");
            $table->timestamp("fecha_emision_doc_modificado")->nullable();
            $table->float("total_sin_impuestos");
            $table->float("valor_modificacion")->nullable();
            $table->json("detalles");
            $table->float("valor_ice");
            $table->float("valor_irbpnr");
            $table->json("razones");
            $table->json("motivos_adicionales")->nullable();
            $table->string("xmlUrl");
            $table->string("pdfUrl");
            $table->boolean('is_downloaded')->default(false);
            $table->foreignUuid('tenant_id')->references('id')->on('tenants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
    }
};
