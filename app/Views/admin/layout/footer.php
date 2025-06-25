            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="<?= base_url('assets/lib/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- jQuery -->
    <script src="<?= base_url('assets/lib/jquery/jquery.min.js') ?>"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>

    <script>
        // Toggle sidebar
        document.getElementById('menu-toggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });

        // Auto-close alerts after 5 seconds
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 5000);
    </script>

    <!-- Page-specific scripts -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= base_url('assets/js/' . $script . '.js') ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
