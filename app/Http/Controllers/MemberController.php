<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PA\ProvinceTh\Factory;

class MemberController extends Controller
{
    public static $nationals = array(
        'ไทย',
        'Afghan',
        'Albanian',
        'Algerian',
        'American',
        'Andorran',
        'Angolan',
        'Antiguans',
        'Argentinean',
        'Armenian',
        'Australian',
        'Austrian',
        'Azerbaijani',
        'Bahamian',
        'Bahraini',
        'Bangladeshi',
        'Barbadian',
        'Barbudans',
        'Batswana',
        'Belarusian',
        'Belgian',
        'Belizean',
        'Beninese',
        'Bhutanese',
        'Bolivian',
        'Bosnian',
        'Brazilian',
        'British',
        'Bruneian',
        'Bulgarian',
        'Burkinabe',
        'Burmese',
        'Burundian',
        'Cambodian',
        'Cameroonian',
        'Canadian',
        'Cape Verdean',
        'Central African',
        'Chadian',
        'Chilean',
        'Chinese',
        'Colombian',
        'Comoran',
        'Congolese',
        'Costa Rican',
        'Croatian',
        'Cuban',
        'Cypriot',
        'Czech',
        'Danish',
        'Djibouti',
        'Dominican',
        'Dutch',
        'East Timorese',
        'Ecuadorean',
        'Egyptian',
        'Emirian',
        'Equatorial Guinean',
        'Eritrean',
        'Estonian',
        'Ethiopian',
        'Fijian',
        'Filipino',
        'Finnish',
        'French',
        'Gabonese',
        'Gambian',
        'Georgian',
        'German',
        'Ghanaian',
        'Greek',
        'Grenadian',
        'Guatemalan',
        'Guinea-Bissauan',
        'Guinean',
        'Guyanese',
        'Haitian',
        'Herzegovinian',
        'Honduran',
        'Hungarian',
        'I-Kiribati',
        'Icelander',
        'Indian',
        'Indonesian',
        'Iranian',
        'Iraqi',
        'Irish',
        'Israeli',
        'Italian',
        'Ivorian',
        'Jamaican',
        'Japanese',
        'Jordanian',
        'Kazakhstani',
        'Kenyan',
        'Kittian and Nevisian',
        'Kuwaiti',
        'Kyrgyz',
        'Laotian',
        'Latvian',
        'Lebanese',
        'Liberian',
        'Libyan',
        'Liechtensteiner',
        'Lithuanian',
        'Luxembourger',
        'Macedonian',
        'Malagasy',
        'Malawian',
        'Malaysian',
        'Maldivan',
        'Malian',
        'Maltese',
        'Marshallese',
        'Mauritanian',
        'Mauritian',
        'Mexican',
        'Micronesian',
        'Moldovan',
        'Monacan',
        'Mongolian',
        'Moroccan',
        'Mosotho',
        'Motswana',
        'Mozambican',
        'Namibian',
        'Nauruan',
        'Nepali',
        'New Zealander',
        'Nicaraguan',
        'Nigerian',
        'Nigerien',
        'North Korean',
        'Northern Irish',
        'Norwegian',
        'Omani',
        'Pakistani',
        'Palauan',
        'Panamanian',
        'Papua New Guinean',
        'Paraguayan',
        'Peruvian',
        'Polish',
        'Portuguese',
        'Qatari',
        'Romanian',
        'Russian',
        'Rwandan',
        'Saint Lucian',
        'Salvadoran',
        'Samoan',
        'San Marinese',
        'Sao Tomean',
        'Saudi',
        'Scottish',
        'Senegalese',
        'Serbian',
        'Seychellois',
        'Sierra Leonean',
        'Singaporean',
        'Slovakian',
        'Slovenian',
        'Solomon Islander',
        'Somali',
        'South African',
        'South Korean',
        'Spanish',
        'Sri Lankan',
        'Sudanese',
        'Surinamer',
        'Swazi',
        'Swedish',
        'Swiss',
        'Syrian',
        'Taiwanese',
        'Tajik',
        'Tanzanian',
        'Togolese',
        'Tongan',
        'Trinidadian/Tobagonian',
        'Tunisian',
        'Turkish',
        'Tuvaluan',
        'Ugandan',
        'Ukrainian',
        'Uruguayan',
        'Uzbekistani',
        'Venezuelan',
        'Vietnamese',
        'Welsh',
        'Yemenite',
        'Zambian',
        'Zimbabwean'
    );

    public function index(Request $request){
        $member = Member::first();

        return $member;
    }
    public function compareByNameTh($a, $b) {
        return strcmp($a['name_th'], $b['name_th']);
    }

    public function create()
    {
        $provinces = Factory::province();
        $provinceArray = $provinces->toArray();
        usort($provinceArray, array($this, 'compareByNameTh'));

        $data = [];
        $data["nations"] = MemberController::$nationals;
        $data["provinces"] = $provinceArray;

        return view('members.create',$data); // Assumes you have a view at resources/views/members/create.blade.php
    }



    /**
     * Store a newly created member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'gender' => 'required|in:ชาย,หญิง',
            'name' => 'required|max:32',
            'surname' => 'required|max:128',
            'nickname' => 'nullable|max:32',
            'age' => 'nullable|integer|min:0',
            'birthdate' => 'required|date',
            'buddhism' => 'nullable|in:ภิกษุ,สามเณร,แม่ชี,ฆราวาส',
            'status' => 'nullable|in:ผู้สมัครใหม่,ศิษย์อานาปานสติ,ศิษย์เตโชวิปัสสนา,ศิษย์อานาฯ ๑ วัน',
            'phone_slug' => 'nullable|max:128',
            'phone' => 'required|max:128',
            'phone_2' => 'nullable|max:20',
            'blacklist' => 'nullable|in:yes,no',
            'line' => 'nullable|max:100',
            'email' => 'required|email|max:256',
            'province' => 'nullable|max:64',
            'country' => 'nullable|max:64',
            'facebook' => 'nullable|max:256',
            'organization' => 'nullable',
            'expertise' => 'nullable',
            'degree' => 'nullable',
            'career' => 'nullable',
            'techo_year' => 'nullable|integer|min:0',
            'techo_courses' => 'nullable|integer|min:0',
            'blacklist_release' => 'nullable|date',
            'blacklist_remark' => 'nullable',
            'pseudo' => 'nullable|max:13',
            'url_apply' => 'nullable|max:1024',
            'url_history' => 'nullable|max:1024',
            'url_image' => 'nullable|max:1024',
            'dharma_ex_desc' => 'nullable',
            'dharma_ex' => 'nullable|max:255',
            'know_source' => 'nullable|max:255',
            'name_emergency' => 'nullable|max:50',
            'surname_emergency' => 'nullable|max:50',
            'phone_emergency' => 'nullable|max:50',
            'relation_emergency' => 'nullable|max:100',
            'create_complete' => 'nullable|boolean',
            'nationality' => 'nullable|max:255'
        ]);
        $validatedData['email'] = Auth::user()->email; // Link member to user by email

        Member::create($validatedData);

        return redirect()->route('profile')->with('success', 'Member created successfully.');
    }

    /**
     * Show the form for editing the specified member.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $member = Member::findOrFail($id); // Ensure the member exists
        $provinces = Factory::province();
        $provinceArray = $provinces->toArray();
        usort($provinceArray, array($this, 'compareByNameTh'));

        $data = [];
        $data["nations"] = MemberController::$nationals;
        $data["provinces"] = $provinceArray;
        $data["member"] = $member;


        return view('members.edit', $data); // Assumes you have a view at resources/views/members/edit.blade.php
    }

    /**
     * Update the specified member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
        ]);

        $member_id = $request->member_id;
        $recheck_member_id = $request->rechecking_id;

        if ($member_id != $recheck_member_id){
            return redirect()->route('profile')->with('success', 'Member edit failed');
        }

        $member = Member::findOrFail($member_id);

        if (!$member){
            return redirect()->route('profile')->with('success', 'Member edit failed');
        }

        $member->update($request->input());

        return back()->with('success',  'Member updated successfully.');

    }
}
