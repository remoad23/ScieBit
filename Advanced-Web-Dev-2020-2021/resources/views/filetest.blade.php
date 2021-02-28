<form action="{{route('filetestload')}}" method="post" enctype="multipart/form-data" >
    @csrf
    <input type="file" name="filer" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>
