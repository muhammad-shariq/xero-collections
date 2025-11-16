<?php

namespace App\Services;
use App\Repositories\TemplateRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreTemplateRequest;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TemplateService{

    private $templateRepository;

    public function __construct(TemplateRepository $templateRepository){
        $this->templateRepository = $templateRepository;
    }

    public function getAll($pagination = false, int $perPage = 15, array $sort = [], array $filters = [], $default = false){
        if($pagination){
            return $this->templateRepository->paginate($perPage, $sort, $filters);
        }

        return $this->templateRepository->all($default);
    }

    public function findByUserID(int $user_id): Collection
    {
        return $this->templateRepository->findByUserID($user_id);
    }

    public function findById(int $id, $array = false): Template | array
    {
        $data = $this->templateRepository->find($id);
        $data->document_link = public_path('files/'.$data->document);

        if( $array ){
            $data = $data->toArray();
        }
        return $data;
    }

    public function findByTemplateName( $template_name)
    {
        return $this->templateRepository->findByTemplateName($template_name);
    }

    public function save(StoreTemplateRequest $request){

        $validated_data = $request->safe()->only(['name', 'email_name_from', 'from_email', 'email_subject', 'template', 'document']);

        $file = $request->file('document');
        $path = Storage::putFileAs(
             $request->file('document'), $file->getClientOriginalName()
        );

        $result = [
            'user_id' => $request->user()->id,
            'document' => $file->getClientOriginalName(),
            'name' => $validated_data['name'],
            'email_name' => $validated_data['email_name_from'],
            'email_from' => $validated_data['from_email'],
            'email_subject' => $validated_data['email_subject'],
            'email_message' => $validated_data['template'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        return $this->templateRepository->create($result);
    }

    public function update(StoreTemplateRequest $request)
    {
        $validated_data = $request->safe()->only(['name', 'email_name_from', 'from_email', 'email_subject', 'template', 'document']);

        $document = $validated_data['document'];
        if($request->file('document'))
        {
            $file = $request->file('document');
            Storage::putFileAs(
                $request->file('document'), $file->getClientOriginalName()
            );

            $document = $file->getClientOriginalName();
        }

        $update = [
            'name' => $validated_data['name'],
            'email_name' => $validated_data['email_name_from'],
            'email_from' => $validated_data['from_email'],
            'email_subject' => $validated_data['email_subject'],
            'email_message' => $validated_data['template'],
            'document' => $document
        ];
        return $this->templateRepository->update($request->id, $update);
    }

    public function delete(int $id)
    {
        $this->templateRepository->delete($id);
    }

}
