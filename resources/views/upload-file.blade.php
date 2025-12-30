@extends('layouts.app-clean')

@section('title', 'Upload File')

@section('content-clean')

    <body class="bg-white">

        <div class="container py-5">
            <div class="mx-auto bg-white shadow-sm rounded-4 p-4" style="max-width: 600px;">
                <h4 class="fw-bold mb-4">Upload File</h4>

                <!-- Upload Form -->
                <form id="uploadForm">
                    @csrf
                    <!-- Upload area -->
                    <div class="upload-box text-center mb-4 border-2 border-dashed rounded-3 p-4" id="uploadArea"
                        style="border-color: #e9ecef; cursor: pointer; transition: all 0.3s ease;">
                        <input type="file" name="file" id="fileInput" class="d-none"
                            accept=".csv,.docx,.pdf,.pptx,.xlsx">
                        <div class="bg-light-orange bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:60px; height:60px;">
                            <i class="ph ph-upload-simple text-dark fs-3" id="uploadIcon"></i>
                        </div>
                        <p class="mb-1 text-secondary">
                            drag & drop files or <span class="text-decoration-none text-orange">click here</span>
                        </p>
                        <small class="text-muted">Supported file types: CSV, DOCX, PDF, PPTX, XLSX</small>
                        <small class="text-muted">Max Size: 10MB</small>
                        <div id="fileName" class="mt-2 text-primary fw-semibold"></div>
                    </div>

                    <!-- Save as -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Save as</label>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="Title Goes Here (Optional)">
                    </div>

                    <!-- Label -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Label</label>
                        <select class="form-select" name="label" id="label">
                            <option value="" selected>Add Label</option>
                            <option value="work">Work</option>
                            <option value="personal">Personal</option>
                            <option value="school">School</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-light border" href="{{ route('dashboard') }}">Cancel</a>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const uploadArea = document.getElementById("uploadArea");
                const fileInput = document.getElementById("fileInput");
                const fileName = document.getElementById("fileName");
                const uploadForm = document.getElementById("uploadForm");

                let selectedFile = null;
                const parentId = 1; // root folder default (ganti sesuai folder aktif kalau perlu)

                // === Klik area untuk pilih file ===
                uploadArea.addEventListener("click", () => fileInput.click());

                // === Drag & Drop ===
                uploadArea.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    uploadArea.classList.add("bg-light");
                });
                uploadArea.addEventListener("dragleave", () => {
                    uploadArea.classList.remove("bg-light");
                });
                uploadArea.addEventListener("drop", (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove("bg-light");
                    if (e.dataTransfer.files.length > 0) {
                        selectedFile = e.dataTransfer.files[0];
                        fileName.textContent = selectedFile.name;
                    }
                });

                // === Saat pilih file lewat dialog ===
                fileInput.addEventListener("change", (e) => {
                    if (e.target.files.length > 0) {
                        selectedFile = e.target.files[0];
                        fileName.textContent = selectedFile.name;
                    }
                });

                // === Submit Form ===
                uploadForm.addEventListener("submit", async (e) => {
                    e.preventDefault();
                    if (!selectedFile) {
                        alert("Please select a file first.");
                        return;
                    }

                    const formData = new FormData();
                    formData.append("files[]", selectedFile);
                    formData.append("relative_paths[]", selectedFile.name);
                    formData.append("parent_id", parentId);

                    try {
                        const response = await axios.post("https://dms-pdu-api.up.railway.app/api/upload-files", formData, {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            },
                        });

                        alert("Upload berhasil!");
                        console.log(response.data);
                        window.location.href = "{{ route('dashboard') }}";
                    } catch (error) {
                        console.error(error);
                        alert("Upload gagal. Coba lagi.");
                    }
                });
            });
        </script>


    @endsection
