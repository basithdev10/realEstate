<?php
/**
 * Leads Management View
 */

requireAuth();

$pageTitle = 'Leads Management';

// Get sales employees for assignment
$salesEmployees = $userModel->getSalesEmployees();

// Handle POST actions
if ($method === 'POST') {
    if ($action === 'create') {
        $result = $leadModel->createLead($_POST);
        if ($result['success']) {
            setFlash('Lead created successfully!', MESSAGE_SUCCESS);
            redirect('?page=leads');
        } else {
            $error = $result['message'] ?? 'Error creating lead';
        }
    } elseif ($action === 'update' && $id) {
        $result = $leadModel->updateLead($id, $_POST);
        if ($result) {
            setFlash('Lead updated successfully!', MESSAGE_SUCCESS);
            redirect('?page=leads&action=view&id=' . $id);
        } else {
            $error = 'Error updating lead';
        }
    } elseif ($action === 'addNote' && $id) {
        $leadModel->addNote($id, $_POST['note'], $_POST['follow_up_date'] ?? null, $_SESSION['user_id']);
        setFlash('Note added successfully!', MESSAGE_SUCCESS);
        redirect('?page=leads&action=view&id=' . $id);
    } elseif ($action === 'updateStage' && $id) {
        $result = $leadModel->updateStage($id, $_POST['stage']);
        if ($result) {
            setFlash('Lead stage updated!', MESSAGE_SUCCESS);
        }
        redirect('?page=leads&action=view&id=' . $id);
    }
}

// Get flash message
$flash = getFlash();

// Determine view
if ($action === 'create') {
    include __DIR__ . '/create.php';
} elseif ($action === 'view' && $id) {
    $lead = $leadModel->getLeadById($id);
    if (!$lead) {
        die('Lead not found');
    }
    include __DIR__ . '/view.php';
} elseif ($action === 'edit' && $id) {
    $lead = $leadModel->getLeadById($id);
    if (!$lead) {
        die('Lead not found');
    }
    include __DIR__ . '/edit.php';
} else {
    // List leads
    $filters = [
        'stage' => $_GET['stage'] ?? '',
        'assigned_to' => $_GET['assigned_to'] ?? '',
        'search' => $_GET['search'] ?? ''
    ];
    
    $page = intval($_GET['p'] ?? 1);
    $limit = ITEMS_PER_PAGE;
    $offset = ($page - 1) * $limit;
    
    $leads = $leadModel->getLeads($filters, $limit, $offset);
    $totalLeads = $leadModel->countLeads($filters);
    $totalPages = ceil($totalLeads / $limit);
    
    include __DIR__ . '/list.php';
}
?>
