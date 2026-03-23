 <?php
    require_once 'Database.php.php';

 ?>
 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
     <meta name="description" content="" />
     <meta name="author" content="" />
     <title>Shop Item - Start Bootstrap Template</title>
     <!-- Favicon-->
     <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
     <!-- Bootstrap icons-->

     <!-- Core theme CSS (includes Bootstrap)-->
     <link href="bootstrap-5.3.8/css/bootstrap.min.css" rel="stylesheet" />
 </head>

 <body>
     <div class="container">
         <h1>Form Trắng</h1>
         <form action="" method="post">
             <div class="form-group">
                 <label for="field1">Ten</label>
                 <input id="field1" name="field1" type="text" placeholder="Nhập..." />
             </div>

             <div class="form- group">
                 <label for="field2">Email</label>
                 <input id="field2" name="field2" type="text" placeholder="Nhập..." />
             </div>

             <div class="form-group">
                 <label for="field3">SDT</label>
                 <input id="field3" name="field3" placeholder="Nhập nội dung..."></input>
             </div>

             <div class="form-group">
                 <label for="field4">Lựa chọn</label>
                 <select id="field4" name="field4">
                     <option value="">-- Chọn --</option>
                     <option value="1">Khach Hang</option>
                 </select>
             </div>
             <div class="actions">
                 <button type="reset" class="secondary">Xóa</button>
                 <button type="submit" class="primary">Gửi</button>
             </div>
         </form>
     </div>
 </body>

 </html>