<?php
chdir('/internal');require 'vendor/autoload.php';$app=require 'bootstrap/app.php';$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$request=Illuminate\Http\Request::create('/admin/products/create','GET');$app->instance('request',$request);
$html=view('quan_tri.TTB_bieu_mau_san_pham',['product'=>null,'categories'=>collect(),'brands'=>collect()])->render();
echo json_encode(['rendered'=>strlen($html)>1000,'bytes'=>strlen($html),'has_create_title'=>str_contains($html,'Thêm sản phẩm mới')],JSON_UNESCAPED_UNICODE);
?>