<footer class="main-footer">
    <div class="footer-right ">
        Copyright &copy; <?php echo date('Y'); ?>
        <div class="bullet"></div><i class="fa fa-wheelchair" aria-hidden="true"></i> Pwd County
    </div>
    <div class="footer-right">

    </div>
</footer>
</div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function (e) {
        Swal.fire({
            title: 'Are you sure you want to logout?',
            text: "You will need to login again to access your account.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'No, stay logged in'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to logout.php
                window.location.href = 'logout.php';
            }
        });
    });
</script>

<!-- General JS Scripts -->
<script src="../assets/modules/jquery.min.js"></script>
<script src="../assets/modules/popper.js"></script>
<script src="../assets/modules/tooltip.js"></script>
<script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
<script src="../assets/modules/moment.min.js"></script>
<script src="../assets/js/stisla.js"></script>

<!-- JS Libraies -->
<script src="../assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>
<script src="../assets/modules/chart.min.js"></script>
<script src="../assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
<script src="../assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
<script src="../assets/modules/summernote/summernote-bs4.js"></script>
<script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

<!-- Page Specific JS File -->
<script src="../assets/js/page/index-0.js"></script>

<!-- Template JS File -->
<script src="../assets/js/scripts.js"></script>
<script src="../assets/js/custom.js"></script>

<!-- sweetalert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>


</body>

</html>