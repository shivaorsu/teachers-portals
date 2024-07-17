<!DOCTYPE html>
<html>
<head>
    <title>Teacher Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .portal-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .portal-container h2 {
            color: #333;
        }
        .portal-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .portal-container button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            position: relative;
            border-radius: 5px;
        }
        .modal-content h2 {
            margin-top: 0;
        }
        .close {
            position: absolute;
            right: 10px;
            top: 10px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }
        .close:before {
            content: "&times;";
        }
        form {
            margin-bottom: 0;
        }
        form button[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        form button[type="submit"]:hover {
            background-color: #218838;
        }
        .button-container {
            display: flex;
            gap: 10px;
        }
        
    </style>
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <div class="portal-container">
        <h2>Welcome to the Teacher Portal</h2>
        <button onclick="showAddStudentModal()">Add New Student</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr data-id="{{ $student->id }}">
                    <td class="name">{{ $student->name }}</td>
                    <td class="subject">{{ $student->subject }}</td>
                    <td class="marks">{{ $student->marks }}</td>
                    <td>
                        <button onclick="editStudent(this)">Edit</button>
                        <form method="POST" action="{{ route('students.destroy', $student->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit form initially hidden -->
                <tr id="editRow{{ $student->id }}" style="display: none;">
                    <td colspan="4">
                        <form method="POST" action="{{ route('students.update', $student->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $student->name }}" required>
                            <input type="text" name="subject" value="{{ $student->subject }}" required>
                            <input type="number" name="marks" value="{{ $student->marks }}" required>
                            <button type="submit">Save</button>
                            <button type="button" onclick="cancelEdit({{ $student->id }})">Cancel</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddStudentModal()">&times;</span>
            <form id="addStudentForm" method="POST" action="{{ route('students.store') }}" onsubmit="return validateForm()">
                @csrf
                <!-- Hidden input field for operation type -->
                <input type="hidden" id="operationType" name="operationType" value="add">
                <h2>Add New Student</h2>
                <input type="text" id="name" name="name" placeholder="Name" required style="margin-bottom: 10px; width: 100%; padding: 8px;">
                <input type="text" id="subject" name="subject" placeholder="Subject" required style="margin-bottom: 10px; width: 100%; padding: 8px;">
                <input type="number" id="marks" name="marks" placeholder="Marks" required style="margin-bottom: 10px; width: 100%; padding: 8px;">
                <button type="submit">Add Student</button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        function showAddStudentModal() {
            var modal = document.getElementById('addStudentModal');
            modal.style.display = 'block';
        }

        function closeAddStudentModal() {
            var modal = document.getElementById('addStudentModal');
            modal.style.display = 'none';
        }

        function checkDuplicate() {
        var name = document.getElementById('name').value;
        var subject = document.getElementById('subject').value;
        var marks = document.getElementById('marks').value;

        // Perform AJAX request to check if student already exists
        axios.post('{{ route("students.checkDuplicate") }}', {
            name: name,
            subject: subject
        })
        .then(function (response) {
            if (response.data.exists) {
                // Student with same name and subject exists, confirm update
                if (confirm('Student with the same name and subject exists. Do you want to update marks?')) {
                    // Set operation type to update
                    document.getElementById('operationType').value = 'update';
                    // Submit form
                    document.getElementById('addStudentForm').submit();
                }
            } else {
                // No existing student found, submit form for addition
                document.getElementById('operationType').value = 'add';
                document.getElementById('addStudentForm').submit();
            }
        })
        .catch(function (error) {
            console.error('Error checking duplicate:', error);
            // Handle error scenario
        });
    }
    function validateForm() {
        var name = document.getElementById('name').value.trim();
        var subject = document.getElementById('subject').value.trim();
        var marks = document.getElementById('marks').value.trim();

        // Basic validation
        if (name === '' || subject === '' || marks === '') {
            alert('Please fill in all fields.');
            return false;
        }

        // Additional validation (e.g., marks should be a number)
        if (isNaN(marks)) {
            alert('Marks must be a number.');
            return false;
        }

        return true; // Form submission allowed
    }
function editStudent(button) {
            var row = button.parentNode.parentNode;
            var editRow = document.getElementById('editRow' + row.dataset.id);
            
            // Hide current row, show edit form row
            row.style.display = 'none';
            editRow.style.display = 'table-row';
        }

        function cancelEdit(studentId) {
            var editRow = document.getElementById('editRow' + studentId);
            var originalRow = editRow.previousElementSibling;
            
            // Show original row, hide edit form row
            originalRow.style.display = 'table-row';
            editRow.style.display = 'none';
        }
    </script>
</body>
</html>
