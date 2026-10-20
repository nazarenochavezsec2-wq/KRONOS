</main>
<footer class="admin-footer"> 
    <div class="admin-footer-inner"> 
        <span class="admin-footer-brand">HIVE_ARCHIVE</span> 
        <span class="admin-footer-text"> 
            Panel de administración del sitio web 
        </span> 
    </div> 
</footer> 

<!-- Bootstrap JavaScript --> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" ></script>

<!-- jQuery --> 
<script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script> 

<!-- DataTables JavaScript --> 
<script src="https://cdn.datatables.net/v/dt/dt-3.0.4/datatables.min.js" ></script> 

<!-- SweetAlert2 --> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script> 
    $(function () { 
        $("table").DataTable({ 
            pageLength: 3 
            lengthMenu: [3, 5, 10, 25]
            language.ajax: language: { 
                ajax: "https://cdn.datatables.net/plug-ins/3.0.4/i18n/es-ES.json" 
            }
        }); 
    }); 
</script>

<?php 
$mensajes = [ 
    "creado" => "Registro agregado con éxito.", 
    "actualizado" => "Registro actualizado con éxito.", 
    "eliminado" => "Registro eliminado con éxito." 
]; 
?> 

<?php if ( 
    isset($_GET["mensaje"]) && 
    isset($mensajes[$_GET["mensaje"]]) 
) { ?> 
 
     <!-- Mensaje de SweetAlert2 --> 
     <script> 
         Swal.fire({ 
              icon: "success", 
              title: <?php echo json_encode( 
                $mensajes[$_GET["mensaje"]], 
                JSON_UNESCAPED_UNICODE 
                ); ?> 
 
         }); 
     </script> 
 
<?php } ?>

</body> 
</html>