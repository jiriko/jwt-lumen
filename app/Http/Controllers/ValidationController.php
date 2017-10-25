<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    /**
     * Used for Xeditable validation
     *
     * Validates a field based on validation types
     *
     * @Request({"type": "unique,email,required", "q": "janedoe@gmail.com"})
     * @GET("/validation{?field,t,id}")
     */
    public function __invoke()
    {
        $this->verifyInput();

        $validator = Validator::make(
            ['q' => request('q')],
            ['q' => $this->getValidationRules()]
        );

        if ($validator->fails()) {
            abort(422);
        }

        return response([]);
    }

    /**
     * This will create the validation rules
     * based on request('type')
     * ex: email,required,unique
     * @return String rules
     */
    private function getValidationRules()
    {
        $rules = $this->getSimpleRules();

        foreach ($this->getComplexRules() as $rule) {
            $rules->push(
                $this->{'get' . $rule . 'Rule'}()
            );
        }

        return $rules->values()->implode('|');
    }

    /**
     * This is for rules like unique, max etc
     * which requires parameters
     *
     * @return static
     */
    private function getComplexRules()
    {
        return collect(['unique'])->filter(function ($rule) {
            return in_array($rule, explode(',', request('type')));
        });
    }

    /**
     * This is for simple rules like numeric, email, require etc..
     * which do not require parameters
     * @return static
     */
    private function getSimpleRules()
    {
        return collect(explode(',', request('type')))->filter(function ($rule) {
            return !in_array($rule, $this->getComplexRules()->all());
        });
    }

    /**
     * Look up for the table currently used for unique rule
     * by request('t')
     *
     * @return mixed
     */
    private function getTable()
    {
        $tables = [
            '1' => 'users',
            '2' => 'students',
            '3' => 'subjects'
        ];

        if (!array_key_exists(request('t'), $tables)) {
            abort(422, 'Invalid table.');
        }

        return $tables[request('t')];
    }

    /**
     * The basic fields are required: request('type') ex: email,required
     * and request('q) is the value of the field to be validated.
     *
     */
    private function verifyInput()
    {
        $this->validate(request(), [
            'type' => 'required',
            'q' => 'required'
        ]);
    }

    //make it a class later
    private function getUniqueRule()
    {
        $validator = Validator::make(
            [
                'field' => request()->get('field'),
                't' => request()->get('t')
            ],
            [
                'field' => 'required',
                't' => 'required'
            ]
        );

        if ($validator->fails()) {
            abort(422);
        }

        if (request()->get('id')) {
            return vsprintf('unique:%s,%s,%s', [$this->getTable(), request('field'), request()->get('id')]);
        }

        return vsprintf('unique:%s,%s', [$this->getTable(), request('field')]);
    }

}
