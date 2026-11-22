<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $developers = [
            [
                'name' => 'Md. Tamim Hasan Siam',
                'student_id' => '0802410205101051',
                'role' => 'Backend & Systems Developer',
                'image' => 'dev/siam.jpg',
                'github' => 'https://github.com/tamim-hasan-01',
                'facebook' => 'https://www.facebook.com/search/top?q=Md.%20Tamim%20Hasan%20Siam',
                'linkedin' => 'https://www.linkedin.com/search/results/all/?keywords=Md%20Tamim%20Hasan%20Siam',
                'x' => 'https://x.com/search?q=Md%20Tamim%20Hasan%20Siam&src=typed_query',
                'email' => 'htamim495@gmail.com',
                'year' => 'CSE · BAUST · 3rd Year',
                'summary' => 'A BAUST CSE student interested in software development, problem solving, Android applications, embedded systems, Linux, and competitive gaming.',
                'skills' => ['C', 'C++', 'Python', 'Java', 'Linux', 'Git', 'MySQL', 'Arduino'],
            ],
            [
                'name' => 'Muhammad Mobasher Ahmmed',
                'student_id' => '0802410205101061',
                'role' => 'Mobile & Application Developer',
                'image' => 'dev/mobahir.jpg',
                'github' => 'https://github.com/MAh-med',
                'facebook' => 'https://www.facebook.com/search/top?q=Muhammad%20Mobasher%20Ahmmed%20Milon',
                'linkedin' => 'https://www.linkedin.com/search/results/all/?keywords=Muhammad%20Mobasher%20Ahmmed%20Milon',
                'x' => 'https://x.com/search?q=Muhammad%20Mobasher%20Ahmmed%20Milon&src=typed_query',
                'email' => null,
                'year' => 'CSE · BAUST · 3rd Year',
                'summary' => 'A CSE student and application developer focused on Kotlin-based mobile products, problem solving, and socially useful digital tools.',
                'skills' => ['Kotlin', 'Android', 'HTML', 'Problem Solving'],
            ],
            [
                'name' => 'Waliur Rafiq Sami',
                'student_id' => '0802410205101088',
                'role' => 'Full-Stack Web Developer',
                'image' => 'dev/sami.png',
                'github' => 'https://github.com/Waliur-Rafiq-Sami',
                'facebook' => 'https://www.facebook.com/search/top?q=Waliur%20Rafiq%20Sami',
                'linkedin' => 'https://www.linkedin.com/search/results/all/?keywords=Waliur%20Rafiq%20Sami',
                'x' => 'https://x.com/search?q=Waliur%20Rafiq%20Sami&src=typed_query',
                'email' => 'waliurrafiqsami@gmail.com',
                'year' => 'CSE · BAUST · 3rd Year',
                'summary' => 'A full-stack developer and CSE student building modern, scalable web experiences with React, Next.js, Laravel, Node.js, and modern state-management tools.',
                'skills' => ['Next.js', 'React', 'TypeScript', 'Laravel', 'Node.js', 'MongoDB', 'Tailwind CSS'],
            ],
        ];

        return view('pages.about', compact('developers'));
    }
}
