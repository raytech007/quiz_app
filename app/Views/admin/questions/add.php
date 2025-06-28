<?php echo view('admin/layout/header'); ?>

<div class="row my-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Question</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/questions/create') ?>" method="post" id="questionForm">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="question_type_id" class="form-label">Question Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="question_type_id" name="question_type_id" required>
                                    <option value="">Select Question Type</option>
                                    <?php foreach ($questionTypes as $type): ?>
                                        <option value="<?= $type['id'] ?>" <?= old('question_type_id') == $type['id'] ? 'selected' : '' ?>>
                                            <?= esc($type['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question_type_id'])): ?>
                                    <div class="text-danger"><?= $errors['question_type_id'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category (Optional)</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                            <?= esc($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="difficulty_level" class="form-label">Difficulty Level</label>
                        <select class="form-select" id="difficulty_level" name="difficulty_level">
                            <option value="easy" <?= old('difficulty_level') == 'easy' ? 'selected' : '' ?>>Easy</option>
                            <option value="medium" <?= old('difficulty_level') == 'medium' || !old('difficulty_level') ? 'selected' : '' ?>>Medium</option>
                            <option value="hard" <?= old('difficulty_level') == 'hard' ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Question Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="5" required><?= old('content') ?></textarea>
                        <?php if (isset($errors['content'])): ?>
                            <div class="text-danger"><?= $errors['content'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="explanation" class="form-label">Explanation (Optional)</label>
                        <textarea class="form-control" id="explanation" name="explanation" rows="3"><?= old('explanation') ?></textarea>
                    </div>

                    <!-- Multiple Choice Options -->
                    <div id="multiple-choice-options" class="question-options" style="display: none;">
                        <h6>Answer Options</h6>
                        <div id="options-container">
                            <div class="option-item mb-3">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="options[0][text]" placeholder="Option 1">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="correct_option" value="0">
                                            <label class="form-check-label">Correct</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="option-item mb-3">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="options[1][text]" placeholder="Option 2">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="correct_option" value="1">
                                            <label class="form-check-label">Correct</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-option">Add Option</button>
                    </div>

                    <!-- True/False Options -->
                    <div id="true-false-options" class="question-options" style="display: none;">
                        <h6>Correct Answer</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="tf_correct" value="true" id="tf_true">
                            <label class="form-check-label" for="tf_true">True</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="tf_correct" value="false" id="tf_false">
                            <label class="form-check-label" for="tf_false">False</label>
                        </div>
                    </div>

                    <!-- Fill in the Blank Answers -->
                    <div id="fill-blank-answers" class="question-options" style="display: none;">
                        <h6>Correct Answers</h6>
                        <div id="answers-container">
                            <div class="answer-item mb-3">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="answers[0][text]" placeholder="Correct Answer 1">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="answers[0][is_case_sensitive]" value="1">
                                            <label class="form-check-label">Case Sensitive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-answer">Add Answer</button>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Question
                        </button>
                        <a href="<?= base_url('admin/questions') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Questions
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor
    CKEDITOR.replace('content', {
        height: 200,
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
            { name: 'insert', items: ['Image', 'Table'] },
            { name: 'tools', items: ['Maximize'] }
        ]
    });

    CKEDITOR.replace('explanation', {
        height: 150,
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList'] }
        ]
    });

    // Question type change handler
    const questionTypeSelect = document.getElementById('question_type_id');
    const optionContainers = document.querySelectorAll('.question-options');

    questionTypeSelect.addEventListener('change', function() {
        // Hide all option containers
        optionContainers.forEach(container => {
            container.style.display = 'none';
        });

        // Show relevant container based on question type
        const selectedType = this.value;
        if (selectedType == '1') { // Multiple choice
            document.getElementById('multiple-choice-options').style.display = 'block';
        } else if (selectedType == '2') { // True/False
            document.getElementById('true-false-options').style.display = 'block';
        } else if (selectedType == '3') { // Fill in the blank
            document.getElementById('fill-blank-answers').style.display = 'block';
        }
    });

    // Add option for multiple choice
    let optionCount = 2;
    document.getElementById('add-option').addEventListener('click', function() {
        const container = document.getElementById('options-container');
        const newOption = document.createElement('div');
        newOption.className = 'option-item mb-3';
        newOption.innerHTML = `
            <div class="row">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="options[${optionCount}][text]" placeholder="Option ${optionCount + 1}">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="correct_option" value="${optionCount}">
                        <label class="form-check-label">Correct</label>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newOption);
        optionCount++;
    });

    // Add answer for fill in the blank
    let answerCount = 1;
    document.getElementById('add-answer').addEventListener('click', function() {
        const container = document.getElementById('answers-container');
        const newAnswer = document.createElement('div');
        newAnswer.className = 'answer-item mb-3';
        newAnswer.innerHTML = `
            <div class="row">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="answers[${answerCount}][text]" placeholder="Correct Answer ${answerCount + 1}">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="answers[${answerCount}][is_case_sensitive]" value="1">
                        <label class="form-check-label">Case Sensitive</label>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newAnswer);
        answerCount++;
    });

    // Form submission handler
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        // Update CKEditor instances
        for (let instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        // Process options/answers based on question type
        const questionType = questionTypeSelect.value;
        
        if (questionType == '1') { // Multiple choice
            const correctOption = document.querySelector('input[name="correct_option"]:checked');
            if (correctOption) {
                const options = document.querySelectorAll('input[name^="options"][name$="[text]"]');
                options.forEach((option, index) => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `options[${index}][is_correct]`;
                    hiddenInput.value = index == correctOption.value ? '1' : '0';
                    this.appendChild(hiddenInput);
                });
            }
        } else if (questionType == '2') { // True/False
            const tfCorrect = document.querySelector('input[name="tf_correct"]:checked');
            if (tfCorrect) {
                // Create true option
                const trueInput = document.createElement('input');
                trueInput.type = 'hidden';
                trueInput.name = 'options[0][text]';
                trueInput.value = 'True';
                this.appendChild(trueInput);

                const trueCorrect = document.createElement('input');
                trueCorrect.type = 'hidden';
                trueCorrect.name = 'options[0][is_correct]';
                trueCorrect.value = tfCorrect.value === 'true' ? '1' : '0';
                this.appendChild(trueCorrect);

                // Create false option
                const falseInput = document.createElement('input');
                falseInput.type = 'hidden';
                falseInput.name = 'options[1][text]';
                falseInput.value = 'False';
                this.appendChild(falseInput);

                const falseCorrect = document.createElement('input');
                falseCorrect.type = 'hidden';
                falseCorrect.name = 'options[1][is_correct]';
                falseCorrect.value = tfCorrect.value === 'false' ? '1' : '0';
                this.appendChild(falseCorrect);
            }
        }
    });
});
</script>

<?php echo view('admin/layout/footer'); ?>