<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;

class AccessDeniedException extends Exception
{
    use ApiResponser;
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->errorResponse(['error' => 'شما اجازه دسترسی به این بخش از سایت را ندارید'], 403);
        }

        abort(403);
    }
}
