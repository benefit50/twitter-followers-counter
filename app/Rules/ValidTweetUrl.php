<?php
namespace App\Rules;

use App\Utils\Validator;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ValidTweetUrl
 * @package App\Rules
 */
class ValidTweetUrl implements Rule
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * ValidTweetUrl constructor.
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->validator->validateUrl($value) === true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid tweet url.';
    }
}
