<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\UseCases\Inquiry\StoreAction;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request, StoreAction $action): RedirectResponse
    {
        $action($request->safe()->only(['name', 'email', 'message']));

        return redirect()->to(route('home').'#contact')
            ->with('inquiry_sent', true);
    }
}
