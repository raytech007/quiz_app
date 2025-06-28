<?php echo view('admin/layout/header'); ?>

<div class="row my-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Question Management</h5>
                <div>
                    <a href="<?= base_url('admin/questions/import') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-upload me-1"></i>Import
                    </a>
                    <a href="<?= base_url('admin/questions/export') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-download me-1"></i>Export
                    </a>
                    <a href="<?= base_url('admin/questions/add') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Question
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($questions)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No questions found. <a href="<?= base_url('admin/questions/add') ?>">Create your first question</a>.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="questionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Content</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Difficulty</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $question): ?>
                                    <tr>
                                        <td><?= $question['id'] ?></td>
                                        <td>
                                            <div class="question-preview">
                                                <?= character_limiter(strip_tags($question['content']), 100) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= ucfirst(str_replace('_', ' ', $question['question_type'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($question['category_name']): ?>
                                                <span class="badge bg-secondary"><?= esc($question['category_name']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">No category</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $question['difficulty_level'] == 'easy' ? 'success' : ($question['difficulty_level'] == 'medium' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($question['difficulty_level']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($question['first_name']) ?> <?= esc($question['last_name']) ?></td>
                                        <td>
                                            <?php if ($question['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/questions/edit/' . $question['id']) ?>" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-info" onclick="viewQuestion(<?= $question['id'] ?>)" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="<?= base_url('admin/questions/delete/' . $question['id']) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this question?')" 
                                                   title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Question View Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">Question Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="questionModalBody">
                <!-- Question content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#questionsTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25,
            "language": {
                "emptyTable": "No questions found"
            }
        });
    }
});

function viewQuestion(questionId) {
    // Show loading
    document.getElementById('questionModalBody').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('questionModal'));
    modal.show();
    
    // Load question details via AJAX
    fetch(`/admin/questions/view/${questionId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('questionModalBody').innerHTML = html;
            
            // Initialize MathJax if present
            if (window.MathJax) {
                MathJax.typesetPromise([document.getElementById('questionModalBody')]);
            }
        })
        .catch(error => {
            document.getElementById('questionModalBody').innerHTML = '<div class="alert alert-danger">Error loading question details.</div>';
        });
}
</script>

<?php echo view('admin/layout/footer'); ?>