<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicPageController extends Controller
{
	public function privacyPolicy()
	{
		return view('public_page.privacy_policy', [
			'title'		=> 'Privacy Policy'
		]);
	}

	public function termsConditions()
	{
		return view('public_page.terms_conditions', [
			'title'		=> 'Terms & Conditions'
		]);
	}
}
