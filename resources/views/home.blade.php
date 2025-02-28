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

        table th,
        table td {
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

        .close-button {
            position: absolute;
            right: 10px;
            top: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }

        .close-button:hover,
        .close-button:focus {
            background-color: #d32f2f;
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
                @foreach ($students as $student)
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

                <tr id="editRow{{ $student->id }}" style="display: none;">
                    <td colspan="4">
                        <form method="POST" action="{{ route('students.update', $student->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $student->name }}" required>
                            <input type="text" name="subject" value="{{ $student->subject }}" required>
                            <input type="number" name="marks" value="{{ $student->marks }}" required>
                            <div class="button-container">
                                <button type="submit">Save</button>
                                <button type="button" onclick="cancelEdit({{ $student->id }})">Cancel</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <button class="close-button" onclick="closeAddStudentModal()">Close</button>
            <form id="addStudentForm" method="POST" action="{{ route('students.store') }}"
                onsubmit="return checkDuplicate(event)">

                @csrf

                <input type="hidden" id="operationType" name="operationType" value="add">
                <h2>Add New Student</h2>
                <input type="text" id="name" name="name" placeholder="Name" required
                    style="margin-bottom: 10px; width: 100%; padding: 8px;">
                <input type="text" id="subject" name="subject" placeholder="Subject" required
                    style="margin-bottom: 10px; width: 100%; padding: 8px;">
                <input type="number" id="marks" name="marks" placeholder="Marks" required
                    style="margin-bottom: 10px; width: 100%; padding: 8px;">
                <button type="submit">Add</button>
            </form>
        </div>
    </div>

    <!-- Modal for Duplicate Student -->
    <div id="duplicateStudentModal" class="modal">
        <div class="modal-content">
            <button class="close-button" onclick="closeDuplicateStudentModal()">Close</button>
            {{-- <span class="close" >&times;</span> --}}
            <h2>Duplicate Student</h2>
            <p>A student with the same name and subject already exists.</p>
        </div>
    </div>

    <script>
        function showAddStudentModal() {
            var modal = document.getElementById('addStudentModal');
            modal.style.display = 'block';
        }

        function closeAddStudentModal() {
            var modal = document.getElementById('addStudentModal');
            modal.style.display = 'none';
        }

        function closeDuplicateStudentModal() {
            var modal = document.getElementById('duplicateStudentModal');
            modal.style.display = 'none';
        }

        async function checkDuplicate(event) {
            event.preventDefault();

            var name = document.getElementById('name').value;
            var subject = document.getElementById('subject').value;

            try {
                let response = await axios.post('{{ route('students.checkDuplicate') }}', {
                    name: name,
                    subject: subject
                });

                if (response.data.exists) {
                    var modal = document.getElementById('duplicateStudentModal');
                    modal.style.display = 'block';
                    return false; // Stop form submission
                } else {
                    document.getElementById('addStudentForm').submit(); // Proceed with form submission
                }
            } catch (error) {
                console.error('Error checking duplicate:', error);
                alert('An error occurred while checking for duplicates. Please try again.');
                return false;
            }
        }

        function editStudent(button) {
            var row = button.parentNode.parentNode;
            var editRow = document.getElementById('editRow' + row.dataset.id);

            row.style.display = 'none';
            editRow.style.display = 'table-row';
        }

        function cancelEdit(studentId) {
            var editRow = document.getElementById('editRow' + studentId);
            var originalRow = editRow.previousElementSibling;

            originalRow.style.display = 'table-row';
            editRow.style.display = 'none';
        }
    </script>
</body>

</html>
