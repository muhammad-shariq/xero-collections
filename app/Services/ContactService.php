<?php

namespace App\Services;
use App\Repositories\ContactRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Common\Xero2Api;

class ContactService{

    private $contactRepository;

    public function __construct(ContactRepository $contactRepository){
        $this->contactRepository = $contactRepository;
    }

    public function getAll($pagination = false, int $perPage = 15, array $sort = [], array $filters = []){
        if($pagination){
            return $this->contactRepository->paginate($perPage, $sort, $filters);
        }
        return $this->contactRepository->all();
    }

    public function updateXeroContact(StoreContactRequest $request)
    {
        $validated_data = $request->safe()->only(['contact_id', 'contact_name', 'contact_email', 'mobile_number']);
        $xero = new Xero2Api($request->user());
        $update = $xero->updateContacts($validated_data);
        if($update['status'] == config('common.success'))
        {
            $data = [
                'name' => $validated_data['contact_name'],
                'email' => $validated_data['contact_email'],
                'mobile_number' => $validated_data['mobile_number']
            ];

            return $this->partial_update($request->id, $data);
        }
        return false;

    }

    public function findById(int $id, bool $array = false): Contact|array
    {
        $q = $this->contactRepository->find($id);
        if( $array )
            $q = $q->toArray();

        return $q;
    }

    public function findByRefContactID(string $contact): array
    {
        $data = $this->contactRepository->findByRefContactID($contact);
        if( $data !== null )
            return $data->toArray();
        return [];
    }

    public function getContactInvoiceEmailCollection(int $template_id)
    {
        return $this->contactRepository->getContactInvoiceEmailCollection($template_id);
    }

    public function getUserDueInvoice(array $data): array
    {
        return $this->contactRepository->getUserDueInvoice($data);
    }

    public function save($data){

        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:64',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile_number' => 'required|max:255',
            'contact_status' => 'required|max:255',
            'ar_outstanding' => 'required',
            'ar_overdue' => 'required',
            'ref_contact_id' => [
                'required',
                function ($attribute, $value, $fail) use($data) {
                    try{
                        $this->contactRepository->findByRefContactID($data['ref_contact_id']);
                        $fail('The '.$attribute.' has already been taken.');
                    }catch(Exception $e){}
                },
            ],
        ]);

        if($validator->fails()){
            throw new ValidationException($validator->errors());
        }

        return $this->contactRepository->create($data);
    }

    public function bulkInsert(array $data): bool
    {
        return $this->contactRepository->bulkInsert($data);
    }

    public function partial_update(int $id, array $data)
    {
        return $this->contactRepository->update($id, $data);
    }

    public function deleteContacts(int|array $contacts)
    {
        $this->contactRepository->delete($contacts);
    }

}
