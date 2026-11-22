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
                'email' => 'htamim495@gmail.com',
                'year' => 'CSE · 3rd Year, 1st Semester',
                'summary' => 'A BAUST CSE student interested in software development, problem solving, Android applications, embedded systems, Linux, and competitive gaming.',
                'skills' => ['C', 'C++', 'Python', 'Java', 'Linux', 'Git', 'MySQL', 'Arduino'],
            ],
            [
                'name' => 'Muhammad Mobasher Ahmmed Milon',
                'student_id' => '0802410205101061',
                'role' => 'Mobile & Application Developer',
                'image' => 'dev/mobashir.jpg',
                'github' => 'https://github.com/MAh-med',
                'email' => null,
                'year' => 'CSE · BAUST',
                'summary' => 'A CSE student and application developer focused on Kotlin-based mobile products, problem solving, and socially useful digital tools.',
                'skills' => ['Kotlin', 'Android', 'HTML', 'Problem Solving'],
            ],
            [
                'name' => 'Waliur Rafiq Sami',
                'student_id' => '0802410205101088',
                'role' => 'Full-Stack Web Developer',
                'image' => 'dev/sami.jpg',
                'github' => 'https://github.com/Waliur-Rafiq-Sami',
                'email' => 'waliurrafiqsami@gmail.com',
                'year' => 'CSE · BAUST',
                'summary' => 'A full-stack developer and CSE student building modern, scalable web experiences with React, Next.js, Laravel, Node.js, and modern state-management tools.',
                'skills' => ['Next.js', 'React', 'TypeScript', 'Laravel', 'Node.js', 'MongoDB', 'Tailwind CSS'],
            ],
        ];

        return view('pages.about', compact('developers'));
    }
}
