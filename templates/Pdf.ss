<!DOCTYPE html>
<html>
<head>
  <title>$Title</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      // ...
    });
  </script>
</head>
<body id="pdf-page">
  <div id="print-area">
    <% if $Trainers %>
      <% loop $Trainers %>
        ...
        ...
      <% end_loop %>
    <% end_if %>
  </div>
</body>
</html>