<?php
@$page = $_GET['q'];
if (!empty($page)) {
    switch ($page) {


        case 'beranda':
            include './pages/beranda/beranda.php';
            break;

        case 'uploads':
            include './pages/uploads/uploads.php';
            break;

        case 'add_uploads':
            include './pages/uploads/add_uploads/add_uploads.php';
            break;

        case 'edit_uploads':
            include './pages/uploads/edit_uploads/edit_uploads.php';
            break;

        case 'delete_uploads':
            include './pages/uploads/delete_uploads/delete_uploads.php';
            break;

        case 'recommendations':
            include './pages/recommendations/recommendations.php';
            break;

        case 'add_recommendations':
            include './pages/recommendations/add_recommendations/add_recommendations.php';
            break;

        case 'edit_recommendations':
            include './pages/recommendations/edit_recommendations/edit_recommendations.php';
            break;

        case 'delete_recommendations':
            include './pages/recommendations/delete_recommendations/delete_recommendations.php';
            break;


        case 'outfit_rules':
            include './pages/outfit_rules/outfit_rules.php';
            break;

        case 'add_outfit_rules':
            include './pages/outfit_rules/add_outfit_rules/add_outfit_rules.php';
            break;

        case 'edit_outfit_rules':
            include './pages/outfit_rules/edit_outfit_rules/edit_outfit_rules.php';
            break;

        case 'delete_outfit_rules':
            include './pages/outfit_rules/delete_outfit_rules/delete_outfit_rules.php';
            break;

        case 'users':
            include './pages/users/users.php';
            break;

        case 'add_users':
            include './pages/users/add_users/add_users.php';
            break;

        case 'edit_users':
            include './pages/users/edit_users/edit_users.php';
            break;

        case 'delete_users':
            include './pages/users/delete_users/delete_users.php';
            break;

        case 'myprofile':
            include './pages/myprofile/myprofile.php';
            break;

        case 'update_profile':
            include './pages/myprofile/update_profile/update_profile.php';
            break;

        case 'hero_section':
            include './pages/hero_section/hero_section.php';
            break;

        case 'add_hero_section':
            include './pages/hero_section/add_hero_section/add_hero_section.php';
            break;

        case 'edit_hero_section':
            include './pages/hero_section/edit_hero_section/edit_hero_section.php';
            break;

        case 'delete_hero_section':
            include './pages/hero_section/delete_hero_section/delete_hero_section.php';
            break;

        case 'features':
            include './pages/features/features.php';
            break;

        case 'add_features':
            include './pages/features/add_features/add_features.php';
            break;

        case 'edit_features':
            include './pages/features/edit_features/edit_features.php';
            break;

        case 'delete_features':
            include './pages/features/delete_features/delete_features.php';
            break;

        case 'about_section':
            include './pages/about_section/about_section.php';
            break;

        case 'add_about_section':
            include './pages/about_section/add_about_section/add_about_section.php';
            break;

        case 'edit_about_section':
            include './pages/about_section/edit_about_section/edit_about_section.php';
            break;

        case 'delete_about_section':
            include './pages/about_section/delete_about_section/delete_about_section.php';
            break;
    }
} else {
    include './pages/beranda/beranda.php';
}