<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الموقع تحت الصيانة | 503</title>
    <!-- استدعاء خط تجوال -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <!-- استدعاء Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- استدعاء FontAwesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 50px 30px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(45deg, #4e73df, #1cc88a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 0;
            opacity: 0.2;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 0;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        .gears-container {
            color: #4e73df;
            margin-bottom: 30px;
            position: relative;
            height: 120px;
        }

        .gear-main {
            font-size: 5rem;
            animation: spin 6s linear infinite;
        }

        .gear-small {
            font-size: 2.5rem;
            color: #1cc88a;
            position: absolute;
            top: 10px;
            right: calc(50% + 30px);
            animation: spin-reverse 4s linear infinite;
        }

        @keyframes spin { 100% { transform: rotate(360deg); } }
        @keyframes spin-reverse { 100% { transform: rotate(-360deg); } }

        h2 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        p {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-refresh {
            background: linear-gradient(45deg, #4e73df, #224abe);
            border: none;
            padding: 12px 35px;
            border-radius: 50px;
            font-weight: bold;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
            transition: all 0.3s ease;
        }

        .btn-refresh:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(78, 115, 223, 0.6);
        }

        .support-text {
            font-size: 0.9rem;
            color: #95a5a6;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="maintenance-card">
        <div class="error-code">503</div>
        
        <div class="content-wrapper">
            <!-- حركة التروس -->
            <div class="gears-container">
                <i class="fas fa-cog gear-main"></i>
                <i class="fas fa-cog gear-small"></i>
            </div>

            <h2>النظام تحت التحديث والصيانة</h2>
            <p>
                عذراً، نقوم حالياً بإجراء بعض التحسينات والتحديثات الهامة لضمان تقديم أفضل تجربة لكم.<br>
                <strong>سنعود للعمل في أقرب وقت ممكن. شكراً لتفهمكم!</strong>
            </p>

            <button onclick="window.location.reload();" class="btn btn-primary btn-refresh btn-lg">
                <i class="fas fa-sync-alt me-2"></i> تحديث الصفحة
            </button>

            <div class="support-text">
                <i class="fas fa-headset me-1"></i> في حالة الطوارئ، يرجى التواصل مع إدارة النظام.
            </div>
        </div>
    </div>

</body>
</html>