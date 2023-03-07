<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use File;
use Buglinjo\LaravelWebp\Facades\Webp;
use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Storage;
use Image;

class EmpleadoControler extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Empleado::all();
        return $employees;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function getB64Image($base64_image){  
        // Obtener el String base-64 de los datos         
        $image_service_str = substr($base64_image, strpos($base64_image, ",")+1);
        // Decodificar ese string y devolver los datos de la imagen        
        $image = base64_decode($image_service_str);   
        // Retornamos el string decodificado
        return $image; 
    }

    public function getB64Extension($base64_image, $full=null){  
        // Obtener mediante una expresión regular la extensión imagen y guardarla
        // en la variable "img_extension"        
        preg_match("/^data:image\/(.*);base64/i",$base64_image, $img_extension);   
        // Dependiendo si se pide la extensión completa o no retornar el arreglo con
        // los datos de la extensión en la posición 0 - 1
        return ($full) ?  $img_extension[0] : $img_extension[1];  
    }    

    public function store(Request $request)
    {
        $employee = new Empleado();
        $employee->name = $request->name;
        $employee->last_name = $request->last_name;
        $employee->job = $request->job;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->age = $request->age;
        
        $img = $this->getB64Image($request->fileimg);
        // Obtener la extensión de la Imagen
        $img_extension = $this->getB64Extension($request->fileimg);
        // Crear un nombre aleatorio para la imagen
        $img_name = time() . '.' . $img_extension;   
        // Usando el Storage guardar en el disco creado anteriormente y pasandole a 
        // la función "put" el nombre de la imagen y los datos de la imagen como 
        // segundo parametro
        Storage::disk('public')->put($img_name, $img);
        $img_path = public_path('/storage/'.$img_name);
        $imgFile = Image::make($img_path);
        $imgFile->resize(350, 350);
        $imgFile->save(public_path('/storage/'.$img_name));

        $employee->photo = $img_name;
        $employee->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Empleado::find($id);
        return $employee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $employee = Empleado::find($id);
        $employee->name = $request->name;
        $employee->last_name = $request->last_name;
        $employee->job = $request->job;
        $employee->phone = $request->phone;
        $employee->address = $request->address;

        $img = $this->getB64Image($request->fileimg);
        // Obtener la extensión de la Imagen
        $img_extension = $this->getB64Extension($request->fileimg);
        // Crear un nombre aleatorio para la imagen
        $img_name = time() . '.' . $img_extension;   
        // Usando el Storage guardar en el disco creado anteriormente y pasandole a 
        // la función "put" el nombre de la imagen y los datos de la imagen como 
        // segundo parametro
        Storage::disk('public')->put($img_name, $img);
        $img_path = public_path('/storage/'.$img_name);
        $imgFile = Image::make($img_path);
        $imgFile->resize(350, 350);
        $imgFile->save(public_path('/storage/'.$img_name));

        $employee->photo = $img_name;
        $employee->age = $request->age;
        $employee->save();
        return $employee;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Empleado::destroy($id);
        return $employee;
    }
}
