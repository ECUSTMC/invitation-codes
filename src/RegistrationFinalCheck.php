<?php

namespace InvitationCodes;

class RegistrationFinalCheck
{
    public function filter($can)
    {
        if (session()->has('using_invitation_code')) {
            $can = session("invitation_codes_original_can");
        }
        session()->forget("invitation_codes_original_can");
      	return $can;
    }
}
