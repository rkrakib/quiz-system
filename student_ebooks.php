<?php
include 'db_connect.php';
$result = $conn->query("SELECT * FROM ebooks ORDER BY uploaded_on DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Books</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #dfe9f3);
            margin: 0;
            padding: 0;
        }

        .ebook-container {
            width: 80%;
            max-width: 950px;
            margin: 50px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            animation: fadeIn 0.6s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .ebook-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .ebook-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
            padding: 15px 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .ebook-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .ebook-title {
            font-size: 1.1rem;
            color: #333;
            font-weight: 500;
        }

        .btn {
            padding: 7px 15px;
            background: #0077cc;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover {
            background: #005fa3;
            transform: scale(1.05);
        }

        .btn.download {
            background: #c1121f;
        }
        .btn.download:hover {
            background: #a80a0a;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(15,15,15,0.85);
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content {
            width: 85%;
            height: 85%;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            animation: slideUp 0.4s ease;
        }

        .modal-header {
            padding: 12px 20px;
            background: #0077cc;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1rem;
        }

        .modal-body {
            flex: 1;
            overflow: hidden;
        }

        .modal-body embed {
            width: 100%;
            height: 100%;
            border: none;
        }

        .close-btn {
            cursor: pointer;
            font-size: 22px;
            font-weight: bold;
            color: white;
            transition: transform 0.2s;
        }
        .close-btn:hover {
            transform: rotate(90deg);
            color: #ffdddd;
        }

        /* Animations */
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.98);}
            to {opacity: 1; transform: scale(1);}
        }
        @keyframes slideUp {
            from {transform: translateY(30px);}
            to {transform: translateY(0);}
        }
    </style>
</head>
<body>
<div class="ebook-container">
    <h2>📚 Available E-Books</h2>
    <div class="ebook-list">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="ebook-item">
                <span class="ebook-title"><?php echo htmlspecialchars($row['file_name']); ?></span>
                <div>
                    <!-- Preview Button -->
                    <a class="btn" href="javascript:void(0);" onclick="openModal('<?php echo $row['file_path']; ?>')">👁 Preview</a>
                    <!-- Download Button -->
                    <a class="btn download" href="<?php echo $row['file_path']; ?>" download>⬇ Download</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal -->
<div id="pdfModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span>📖 PDF Preview</span>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <embed id="pdfViewer" src="" type="application/pdf">
        </div>
    </div>
</div>

<script>
function openModal(pdfPath) {
    document.getElementById('pdfViewer').src = pdfPath;
    document.getElementById('pdfModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('pdfViewer').src = "";
    document.getElementById('pdfModal').style.display = "none";
}
</script>
</body>
</html>
