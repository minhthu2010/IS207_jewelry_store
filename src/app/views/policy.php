<?php
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";
include __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style_policy.css">

<main class="policy-page">
    <div class="link-container">
        <nav class="breadcrumb-nav mb-4">
        <a href="<?= $base_url ?>index.php" class="breadcrumb-link">Trang chủ</a> &nbsp; › &nbsp;
        <span>Chính sách</span>
        </nav>
    </div>
    <section class="policy-hero">
        <div class="container">
            <h1>Chính sách hoạt động</h1>
            <p class="intro">Lara Jewelry cam kết mang đến trải nghiệm mua sắm minh bạch, an toàn và tận tâm.</p>
        </div>
    </section>

    <section class="policy-content">
        <div class="container">

            <div class="policy-block">
                <h2>1. Chính sách bảo hành</h2>
                <ul>
                    <li>Bảo hành làm sáng miễn phí trọn đời cho trang sức bạc.</li>
                    <li>Bảo hành 12 tháng đối với trang sức vàng, kim cương.</li>
                    <li>Không bảo hành khi sản phẩm móp méo, biến dạng do tác động mạnh hoặc tự ý sửa.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>2. Chính sách đổi trả</h2>
                <ul>
                    <li>Đổi trả trong vòng 3 ngày kể từ khi nhận hàng.</li>
                    <li>Sản phẩm còn nguyên vẹn, đầy đủ hộp, hóa đơn, chưa sử dụng.</li>
                    <li>Không áp dụng đổi trả cho sản phẩm đặt theo yêu cầu.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>3. Chính sách giao hàng</h2>
                <ul>
                    <li>Giao hàng toàn quốc qua các đơn vị vận chuyển uy tín.</li>
                    <li>Nội thành: 1-2 ngày, ngoại tỉnh: 2-5 ngày.</li>
                    <li>Miễn phí giao hàng cho tất cả sản phẩm.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>4. Chính sách thanh toán</h2>
                <ul>
                    <li>Thanh toán khi nhận hàng (COD).</li>
                    <li>Chuyển khoản ngân hàng.</li>
                    <li>Thanh toán tại cửa hàng bằng tiền mặt hoặc thẻ.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>5. Chính sách bảo mật thông tin</h2>
                <ul>
                    <li>Không chia sẻ thông tin khách hàng cho bên thứ ba khi chưa được phép.</li>
                    <li>Cam kết bảo mật dữ liệu theo tiêu chuẩn an toàn.</li>
                    <li>Khách hàng có quyền yêu cầu chỉnh sửa hoặc xóa dữ liệu cá nhân.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>6. Chính sách sản phẩm & chất lượng</h2>
                <ul>
                    <li>Sản phẩm được kiểm định chất lượng trước khi bán.</li>
                    <li>Trang sức vàng/kim cương có chứng nhận & phiếu kiểm định.</li>
                    <li>Cam kết thông tin đúng chất liệu, trọng lượng, loại đá.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>7. Chính sách đặt hàng theo yêu cầu</h2>
                <ul>
                    <li>Nhận thiết kế trang sức theo mẫu khách đưa hoặc mẫu độc quyền.</li>
                    <li>Cọc 30-50% tùy thiết kế.</li>
                    <li>Thời gian chế tác: 5-20 ngày.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>8. Điều khoản sử dụng website</h2>
                <ul>
                    <li>Khi sử dụng website đồng nghĩa khách hàng chấp nhận chính sách.</li>
                    <li>Chúng tôi có quyền cập nhật chính sách bất kỳ lúc nào.</li>
                    <li>Nội dung, hình ảnh thuộc Lara Jewelry - không sao chép khi chưa cho phép.</li>
                </ul>
            </div>

            <div class="policy-block">
                <h2>9. Liên hệ hỗ trợ</h2>
                <p><strong>Lara Jewelry - Dịch vụ khách hàng</strong></p>
                <ul>
                    <li>Hotline: 0900 123 456</li>
                    <li>Email: Lara@gmail.com</li>
                    <li>Địa chỉ: Hàn Thuyên, khu phố 6, Thủ Đức, TP. HCM</li>
                <ul>
            </div>

        </div>
    </section>

</main>

<?php
include __DIR__ . '/templates/footer.php';
?>
