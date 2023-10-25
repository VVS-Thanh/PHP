<div
    style="max-width: 600px; margin: 30px auto; padding: 30px; text-align: center; border: 1px solid red; border-radius: 20px;">
    <h2>
        Đã có lỗi xảy ra liên quan đến CSDL
    </h2>
    <div>
        <p>Message: <?php echo $e->getMessage(); ?></p>
        <p>File: <code><?php echo $e->getFile(); ?></code></p>
        <p>Line: <?php echo $e->getLine(); ?></p>
    </div>
</div>