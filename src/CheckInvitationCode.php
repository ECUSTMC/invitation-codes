<?php

namespace InvitationCodes;

use Blessing\Rejection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckInvitationCode
{
    const UNIVERSAL_CODE = 'ECUSTMC';

    const ALLOWED_DOMAINS = [
        'mail.ecust.edu.cn',
        'ecust.edu.cn',
        'alumni.ecust.edu.cn',
    ];

    /** @var Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function filter($can)
    {
        $code = $this->request->input('invitationCode');
        session(['invitation_codes_original_can' => $can]);
        if (empty($code)) {
            return new Rejection(trans('InvitationCodes::messages.empty'));
        }

        if ($code === static::UNIVERSAL_CODE) {
            $email = $this->request->input('email', '');
            if (!$this->isEmailAllowed($email)) {
                return new Rejection(trans('InvitationCodes::messages.domain_not_allowed'));
            }
            session(['using_invitation_code' => $code]);
            session(['using_universal_code' => true]);

            return $can;
        }

        $result = DB::table('invitation_codes')->where('code', $code)->first();

        if ($result && $result->used_by == 0) {
            session(['using_invitation_code' => $code]);

            return $can;
        }

        return new Rejection(trans('InvitationCodes::messages.invalid'));
    }

    protected function isEmailAllowed($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        foreach (static::ALLOWED_DOMAINS as $domain) {
            if (str_ends_with(strtolower($email), '@' . $domain)) {
                return true;
            }
        }

        return false;
    }
}
