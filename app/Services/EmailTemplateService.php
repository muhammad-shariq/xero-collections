<?php

namespace App\Services;
use App\Repositories\EmailTemplateRepository;
use App\Models\EmailTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Carbon\Carbon;
use App\Http\Requests\StoreEmailTemplateRequest;

class EmailTemplateService{

    private $emailTemplateRepository;

    public function __construct(EmailTemplateRepository $emailTemplateRepository){
        $this->emailTemplateRepository = $emailTemplateRepository;
    }

    public function getAll($pagination = false, int $perPage = 15, array $sort = [], array $filters = []){
        if($pagination){
            return $this->emailTemplateRepository->paginate($perPage, $sort, $filters);
        }
        return $this->emailTemplateRepository->all();
    }

    public function getEmailTemplateData(int $user_id = 0, int $email_template = 0): array
    {
        return $this->emailTemplateRepository->getEmailTemplateData($user_id, $email_template);
    }

    public function findById(int $id, bool $array = false): EmailTemplate | array
    {
        $data = $this->emailTemplateRepository->find($id);
        if( $array ){
            $data = $data->toArray();
        }

        return $data;
    }

    public function bulkInsert(array $data): bool
    {
        return $this->emailTemplateRepository->bulkInsert($data);
    }

    public function save(StoreEmailTemplateRequest $request): EmailTemplate
    {
        $validated_data = $request->safe()->only(['template', 'days', 'user_id']);

        $result = [
            'user_id' => $validated_data['user_id'],
            'template_id' => $validated_data['template'],
            'trigger_days' => $validated_data['days'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        return $this->emailTemplateRepository->create($result);
    }

    public function update(StoreEmailTemplateRequest $request)
    {
        $validated_data = $request->safe()->only(['template', 'days', 'user_id']);

        $update = [
            'template_id' => $validated_data['template'],
            'user_id' => $validated_data['user_id'],
            'trigger_days' => $validated_data['days']
        ];

        return $this->emailTemplateRepository->update($request->id, $update);
    }

    public function delete(int $id)
    {
        $this->emailTemplateRepository->delete($id);
    }

}
