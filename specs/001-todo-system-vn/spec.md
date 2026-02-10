# Đặc tả Tính năng: Hệ thống Todo List Cơ bản

**Nhánh Tính năng**: `001-todo-system-vn`  
**Ngày tạo**: 2026-02-10  
**Trạng thái**: Bản nháp  
**Mô tả đầu vào**: "hệ thống todo list cơ bản: thêm, sửa, xóa, đánh dấu hoàn thành, tiêu đề tối đa 255 ký tự. Viết bằng tiếng việt"

## Kịch bản Người dùng & Kiểm thử *(bắt buộc)*

### Câu chuyện Người dùng 1 - Xem và Tạo Todo (Ưu tiên: P1)

Người dùng cần xem danh sách todo của họ và thêm các mục todo mới để theo dõi nhiệm vụ và trách nhiệm.

**Tại sao ưu tiên này**: Chức năng cốt lõi mang lại giá trị ngay lập tức - người dùng có thể bắt đầu quản lý nhiệm vụ ngay. Tạo nền tảng cho tất cả các tính năng khác.

**Kiểm thử độc lập**: Có thể kiểm thử đầy đủ bằng cách hiển thị danh sách trống, thêm nhiều todo, và xác minh chúng xuất hiện trong danh sách với tiêu đề chính xác.

**Kịch bản chấp nhận**:

1. **Cho** danh sách todo trống, **Khi** người dùng truy cập ứng dụng, **Thì** họ thấy thông báo không có todo nào và tùy chọn tạo todo đầu tiên
2. **Cho** form tạo todo, **Khi** người dùng nhập tiêu đề hợp lệ (1-255 ký tự) và gửi, **Thì** todo xuất hiện ngay trong danh sách với trạng thái "chưa hoàn thành"
3. **Cho** form tạo todo, **Khi** người dùng cố gắng gửi với tiêu đề trống, **Thì** hệ thống hiển thị lỗi "Tiêu đề là bắt buộc"
4. **Cho** form tạo todo, **Khi** người dùng nhập tiêu đề dài hơn 255 ký tự, **Thì** hệ thống hiển thị lỗi "Tiêu đề phải có tối đa 255 ký tự"

---

### Câu chuyện Người dùng 2 - Đánh dấu Hoàn thành Todo (Ưu tiên: P2)

Người dùng cần đánh dấu todo đã hoàn thành hoặc chưa hoàn thành để theo dõi tiến độ và cảm nhận thành tựu.

**Tại sao ưu tiên này**: Thiết yếu cho quy trình quản lý nhiệm vụ - không có theo dõi hoàn thành, danh sách todo có giá trị hạn chế.

**Kiểm thử độc lập**: Có thể kiểm thử bằng cách tạo todo, chuyển đổi trạng thái hoàn thành, và xác minh thay đổi hình ảnh và lưu trạng thái.

**Kịch bản chấp nhận**:

1. **Cho** todo chưa hoàn thành trong danh sách, **Khi** người dùng nhấp chuyển đổi hoàn thành, **Thì** todo được đánh dấu hoàn thành với chỉ báo hình ảnh (gạch ngang, dấu tick, v.v.)
2. **Cho** todo đã hoàn thành trong danh sách, **Khi** người dùng nhấp chuyển đổi hoàn thành, **Thì** todo được đánh dấu chưa hoàn thành và bỏ chỉ báo hình ảnh
3. **Cho** nhiều todo với trạng thái hoàn thành hỗn hợp, **Khi** người dùng làm mới trang, **Thì** tất cả trạng thái hoàn thành được bảo toàn chính xác

---

### Câu chuyện Người dùng 3 - Chỉnh sửa Tiêu đề Todo (Ưu tiên: P3)

Người dùng cần chỉnh sửa tiêu đề todo hiện có để sửa lỗi hoặc cập nhật mô tả nhiệm vụ khi yêu cầu thay đổi.

**Tại sao ưu tiên này**: Cải thiện tính linh hoạt và trải nghiệm người dùng bằng cách cho phép sửa đổi và cập nhật, nhưng không thiết yếu cho chức năng cơ bản.

**Kiểm thử độc lập**: Có thể kiểm thử bằng cách tạo todo, chỉnh sửa tiêu đề thành các giá trị khác nhau, và xác minh thay đổi được lưu và xác thực đúng.

**Kịch bản chấp nhận**:

1. **Cho** todo hiện có, **Khi** người dùng bắt đầu chế độ chỉnh sửa và đổi tiêu đề thành giá trị hợp lệ, **Thì** tiêu đề cập nhật được lưu và hiển thị ngay
2. **Cho** todo trong chế độ chỉnh sửa, **Khi** người dùng cố lưu tiêu đề trống, **Thì** hệ thống hiển thị lỗi xác thực và không lưu thay đổi
3. **Cho** todo trong chế độ chỉnh sửa, **Khi** người dùng cố lưu tiêu đề dài hơn 255 ký tự, **Thì** hệ thống hiển thị lỗi xác thực và không lưu thay đổi
4. **Cho** todo trong chế độ chỉnh sửa, **Khi** người dùng hủy chỉnh sửa, **Thì** tiêu đề gốc được giữ nguyên không đổi

---

### Câu chuyện Người dùng 4 - Xóa Todo (Ưu tiên: P4)

Người dùng cần xóa todo không còn cần thiết để giữ danh sách sạch sẽ và tập trung vào nhiệm vụ hiện tại.

**Tại sao ưu tiên này**: Hữu ích để duy trì danh sách nhưng không thiết yếu cho chức năng cốt lõi - người dùng có thể làm việc xung quanh bằng cách để lại các mục đã hoàn thành.

**Kiểm thử độc lập**: Có thể kiểm thử bằng cách tạo todo, xóa chúng từng cái, và xác minh chúng được xóa hoàn toàn khỏi danh sách.

**Kịch bản chấp nhận**:

1. **Cho** todo trong danh sách, **Khi** người dùng nhấp xóa và xác nhận hành động, **Thì** todo bị xóa vĩnh viễn khỏi danh sách
2. **Cho** todo trong danh sách, **Khi** người dùng nhấp xóa nhưng hủy xác nhận, **Thì** todo vẫn không đổi trong danh sách
3. **Cho** todo cuối cùng còn lại, **Khi** người dùng xóa nó, **Thì** danh sách hiển thị thông báo trạng thái trống

---

### Trường hợp Biên

- **Duplicate titles**: Cho phép - Người dùng có thể tạo nhiều todo với tiêu đề giống hệt nhau (không có unique constraint)
- **Network errors**: Hệ thống hiển thị error messages rõ ràng và cho phép retry (Phase 7: tasks T066-T068)
- **Concurrent edits** (multi-user): **Out of scope cho single-user MVP** - Behavior: Last write wins, không có optimistic locking
- **Special characters**: Hỗ trợ đầy đủ UTF-8 - emoji (🎯, 📝), dấu tiếng Việt (ĂĂĂĂĂ), ký tự đặc biệt (@, #, $) đều được chấp nhận
- **Navigation during edit**: Unsaved changes bị mất - Không có auto-save hay warning trong MVP

## Yêu cầu *(bắt buộc)*

### Yêu cầu Chức năng

- **FR-001**: Hệ thống PHẢI cho phép người dùng xem tất cả todo của họ trong giao diện danh sách đơn
- **FR-002**: Hệ thống PHẢI cho phép người dùng tạo todo mới với tiêu đề từ 1-255 ký tự
- **FR-003**: Hệ thống PHẢI xác thực tiêu đề todo và từ chối gửi trống hoặc quá dài với thông báo lỗi rõ ràng
- **FR-004**: Hệ thống PHẢI cho phép người dùng đánh dấu todo đã hoàn thành hoặc chưa hoàn thành
- **FR-005**: Hệ thống PHẢI lưu trạng thái hoàn thành todo giữa các phiên
- **FR-006**: Hệ thống PHẢI cho phép người dùng chỉnh sửa tiêu đề todo hiện có với quy tắc xác thực giống như tạo mới
- **FR-007**: Hệ thống PHẢI cho phép người dùng xóa todo với xác nhận để tránh xóa nhầm
- **FR-008**: Hệ thống PHẢI cung cấp phản hồi hình ảnh cho trạng thái hoàn thành todo (ví dụ: gạch ngang, dấu tick)
- **FR-009**: Hệ thống PHẢI hiển thị trạng thái trống phù hợp khi không có todo nào
- **FR-010**: Hệ thống PHẢI bảo toàn thứ tự todo và dữ liệu khi người dùng làm mới trang

### Thực thể Chính

- **Todo**: Đại diện cho một mục nhiệm vụ với tiêu đề (chuỗi, 1-255 ký tự), trạng thái hoàn thành (boolean), thời gian tạo, và mã định danh duy nhất để theo dõi và lưu trữ

## Tiêu chí Thành công *(bắt buộc)*

### Kết quả Có thể Đo lường

- **SC-001**: Người dùng có thể tạo todo mới trong vòng 10 giây từ khi truy cập ứng dụng
- **SC-002**: Chuyển đổi trạng thái hoàn thành todo cung cấp phản hồi hình ảnh ngay lập tức trong vòng 100ms
- **SC-003**: Hệ thống xử lý ít nhất 1000 todo mỗi người dùng mà không giảm hiệu suất
- **SC-004**: 95% thao tác todo (tạo, sửa, xóa, chuyển đổi) hoàn thành thành công trong điều kiện bình thường
- **SC-005**: Tất cả dữ liệu todo lưu trữ đúng giữa các phiên trình duyệt và làm mới trang
- **SC-006**: Xác thực tiêu đề todo ngăn chặn 100% việc gửi trống và quá dài với thông báo lỗi rõ ràng
