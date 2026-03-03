<!DOCTYPE html>
<html>
<head>
    <title>AI Predictor Dashboard</title>
    <meta http-equiv="refresh" content="5"> <style>
        body { background: #121212; color: white; font-family: sans-serif; text-align: center; }
        .box { background: #1e1e1e; padding: 20px; border-radius: 10px; display: inline-block; margin-top: 50px; border: 2px solid #e91e63; }
        .pred { font-size: 60px; color: #00ff88; }
    </style>
</head>
<body>
    <div class="box">
        <?php 
        $data = json_decode(file_get_contents('data.json'), true);
        if($data): ?>
            <h2>Next Round Prediction</h2>
            <div class="pred"><?php echo $data['next']; ?>x</div>
            <p>Based on Last: <?php echo $data['last']; ?>x</p>
            <small>Updated at: <?php echo $data['time']; ?></small>
        <?php else: ?>
            <p>Waiting for data from Extension...</p>
        <?php endif; ?>
    </div>
</body>
</html>
