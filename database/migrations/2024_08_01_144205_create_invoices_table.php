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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date("fecha_emision");
            $table->date("fecha_autorizacion");
            $table->string("ruc_emisor");
            $table->string("clave_acceso");
            $table->string("razon_social_emisor");
            $table->string("nombre_comercial_emisor")->nullable();
            $table->string("serie_comprobante");
            $table->text("direccion_matriz");
            $table->text("direccion_establecimiento");
            $table->string("tipo_identificacion_comprador")->nullable();
            $table->string("contribuyente_especial")->nullable();
            $table->string("obligado_llevar_contabilidad")->nullable();
            $table->string("razon_social_comprador");
            $table->string("identificacion_comprador");
            $table->float("total_sin_impuestos")->default(0.00);
            $table->float("total_descuento")->default(0.00);
            $table->float("propina")->default(0.00);
            $table->json("detalles");
            $table->float("valor_sin_impuestos")->default(0.00);
            $table->float("valor_ice")->default(0.00);
            $table->float("valor_irbpnr")->default(0.00);
            $table->float("importe_total");
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
        Schema::dropIfExists('invoices');
    }
};
