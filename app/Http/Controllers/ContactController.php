<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;


class ContactController extends Controller
{
    /* Devuelve una lista paginada de contactos */
    public function index()
    {
        /* Obtenemos todos los contactos y también sus teléfonos
            paginate(10) devuelve 10 registros por página
            Esto evita traer demasiados datos de golpe */
        return Contact::with('phones')->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /* Crear un nuevo contacto */
    public function store(StoreContactRequest $request)
        {
            // Creamos un contacto usando solo los campos permitidos
            // El request ya viene validado por StoreContactRequest
            $contact = Contact::create(
                $request->only('name','email','address') // only() evita mass assignment de campos no deseados
            );


            // Si vienen teléfonos en la petición
            if ($request->phones) {

                // Recorremos cada teléfono recibido
                foreach ($request->phones as $phone) {

                    // Creamos el teléfono asociado al contacto
                    // Laravel asigna automáticamente el contact_id
                    $contact->phones()->create([
                        'number' => $phone
                    ]);
                }
            }

            // Devolvemos el contacto con sus teléfonos y código 201 (creado)
            // load('phones') recarga la relación para devolver el estado final
            // 201 es el status HTTP correcto para creación de recursos
            return response()->json(
                $contact->load('phones'),
                201
            );
        }

    /* Mostrar contacto en especifico   */

    public function show(Contact $contact)
    {
        // Laravel recibe automáticamente el contacto por ID
        // y devuelve el contacto con sus teléfonos
        return $contact->load('phones');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /* Actualiza un contacto existente */
    public function update(Request $request, Contact $contact)
    {
        // Actualizamos los datos básicos del contacto
        // El request ya fue validado
        // only() mantiene control sobre qué campos se actualizan
        $contact->update(
            $request->only('name','email','address')
        );

         // Si vienen teléfonos nuevos
        if ($request->phones) {

            // Eliminar todos los teléfonos actuales del contacto
            $contact->phones()->delete();

            // Crear los nuevos teléfonos
            foreach ($request->phones as $phone) {
                $contact->phones()->create([
                    'number' => $phone
                ]);
            }
        }

         // Devolvemos el contacto actualizado con teléfonos
        return $contact->load('phones');

    }

    /* Elimina un contacto */
    public function destroy(Contact $contact)
    {
        // Eliminamos el contacto
        $contact->delete();


        // No Content es el status correcto para deletes
        return response()->noContent();
    }
}
