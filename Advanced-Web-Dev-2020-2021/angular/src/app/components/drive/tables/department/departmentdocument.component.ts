import {AfterViewInit, Component, ViewChild, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {SharedService} from "../../../../Services/shared.service";
import {Router} from "@angular/router";

@Component({
  selector: 'departmentdocument',
  template: `
    <select *ngIf="routerUrl.url === '/department'" class="departmentSelect" #departmentSelection (change)="changeFiles()" >
        <option *ngFor="let department of currentDepartments" [value]="department['id']" >
          {{firstLetterToUppercase(determineDepartmentType(department['id']))}}
        </option>
    </select>

    <router-outlet></router-outlet>

    <div class="droplistGroupContainer" id="folderContainer">
      <div class="droplistFolder" *ngFor="let folder of folders; let k = index;">
        <folder draggable="true" (dragend)="dragEnd()" (dragstart)="dragStart($event,folder_detail)" (dragenter)="dragEnter($event, folder_detail)"
                (dragleave)="dragLeave($event)" (dragover)="allowDrop($event)" (dblclick)="goToNextFolder(folder_detail.id)"
                *ngFor="let folder_detail of folder; index as i" [hashName]="folder_detail.folder" [folderID]="folder_detail.id">
          {{folder_detail.foldername}}
        </folder>
      </div>
    </div>

    <div class="droplistGroupContainer">
      <div class="droplistMydocument" *ngFor="let file of files; let k = index;">
        <file draggable="true" (dragend)="dragEnd()" (dragstart)="dragStart($event,file_details)" *ngFor="let file_details of file; index as i "
              [fileID]="file_details.id" [hashName]="file_details.file" [version_group_id]="file_details.version_group_id" [fileDetails]="file_details">
          {{file_details.filename}}
        </file>
      </div>
    </div>`,
  styles:[`
    :host
    {
      width: 100%;
      height: 56vh;
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      padding: 4%;
    }
    departmentdocument
    {
      width: 100%;
    }
    .departmentSelect
    {
      display: flex;
      width: 100%;
      height: 2vw;
      margin-bottom: 2vw;
    }
    .droplistGroupContainer
    {
      height: auto;
      width: 100%;
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      margin-bottom: 2%;
    }
    .droplistMydocument
    {
      height: 80%;
      width: 100%;
      display: flex;
      flex-direction: row;
      margin-bottom: 1.5%;
    }
    .droplistFolder
    {
      height: 80%;
      width: 100%;
      display: flex;
      flex-direction: row;
      margin-bottom: 1.5%;
    }
    .draggedOver
    {
      transition: 0.2s all;
      transform: scale(1.2);
    }

    @media (max-width: 1270px) {
      .droplistMydocument {
        flex-wrap: wrap;
        margin-bottom: 0;
      }
      .droplistFolder {
        flex-wrap: wrap;
        margin-bottom: 0;
      }

      .departmentSelect {
        height: 4vw;
        margin-bottom: 3vw;
      }
    }

    @media (max-width: 800px) {
      .departmentSelect {
        width: 95%;
        height: 6vw;
        margin-bottom: 5vw;
      }

    }

    @media (max-width: 600px) {

      .droplistMydocument {
        flex-wrap: nowrap;
        margin-bottom: 2%;
        flex-direction: column;
      }

      .draggedOver
      {
        transform: scale(1.1);
      }

      .departmentSelect {
        height: 8vw;
        margin-bottom: 7vw;
      }
    }
  `],
  encapsulation: ViewEncapsulation.None
})

export class DepartmentComponent{

  files: any;
  folders: any;
  loadedFiles: any;
  userSearchFiles: any;
  currentSearchText = '';
  departmentsLoaded = false;
  drive: DriveService;
  dragElement: any;
  dropFolder: any;
  draggedOverFolderElement: any;
  currentDepartments: any;
  fileUploaded;
  @ViewChild('departmentSelection') private departmentSelection;
  public routerUrl;

  constructor(private _drive: DriveService,private shared: SharedService,private router: Router)
  {
    this.files = [];
    this.loadedFiles = [];
    this.folders = [];
    this.userSearchFiles = [];
    this.drive = _drive;
    this.currentDepartments = [];
    this.routerUrl = router;
    this.fileUploaded = {uploaded: false};
    this.drive.determineDepartments(this.currentDepartments);
    shared.insertObject("departmentdocument",this);

  }

  loadFiles() {
      this.drive.getDataByHttp(
        this.determineDepartmentType(this.departmentSelection.nativeElement.value),
        this.loadedFiles);
      this.drive.getDataByHttp(
        this.determineDepartmentType(this.departmentSelection.nativeElement.value)+"files",
        this.userSearchFiles);
      this.drive.getDepartmentFolder(this.folders,this.departmentSelection.nativeElement.value);
      this.files = this.loadedFiles;
      this.departmentsLoaded = true;
  }

  firstLetterToUppercase(string){
    if(!this.departmentsLoaded){
      this.loadFiles();
    }
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  goToNextFolder(id)
  {
    this.shared.getObject("app").fileOrFolderSelected = false;
    this.shared.removeObject("selectedFile");
    this.shared.removeObject("selectedFolder");
    if(this.router.url === "/department")
    {
      this.router.navigate([ "department/folder/"+ id]);

    }
    else{
      this.router.navigate(['department']).then(() =>{
        this.router.navigate([ "department/folder/" + id],{ replaceUrl: true });

      });
    }

  }

  private determineDepartmentType(id: number) : string
  {
    if(id == 1) return "finance"
    if(id == 2) return "controlling"
    if(id == 3) return "development"
    if(id == 4) return "marketing"
    if(id == 5) return "humanresources"
  }

  changeFiles()
  {
    this.loadedFiles = [];
    this.folders = [];
    this.drive.getDataByHttp(
      this.determineDepartmentType(this.departmentSelection.nativeElement.value),
      this.loadedFiles);
    this.drive.getDepartmentFolder(this.folders,this.departmentSelection.nativeElement.value);
    this.files = this.loadedFiles;
  }

  /**
   * sorts the files and folder arrays by name
   */
  sortByName(){
    this.folders = this.drive.sortByNameFolders(this.folders);
    this.files = this.drive.sortByNameFiles(this.files);
  }

  /**
   * sorts the files and folder arrays by date
   */
  sortByDate(){
    this.folders = this.drive.sortByDate(this.folders);
    this.files = this.drive.sortByDate(this.files);
  }

  /**
  * filters the files array for specific filenames
  * @param searchText: text to filter
  */
  searchFiles(searchText){
    let folderContainer = document.getElementById("folderContainer");
    if (!searchText) {
      this.files = this.loadedFiles;
      folderContainer.style.display = "flex";
      return;
    }
    else{
      folderContainer.style.display = "none";
    }
    if(searchText.length < this.currentSearchText.length || searchText.length === 1){
      this.files = this.userSearchFiles;
    }
    this.currentSearchText = searchText;

    searchText = searchText.toLocaleLowerCase();

    let tempArray = [];
        for(let i = 0; i < this.files.length; i++){
          for(let j = 0; j < this.files[i].length; j++){
            tempArray.push(this.files[i][j]);
        }
    }

    tempArray = tempArray.filter(file => {
      return file.filename.toLocaleLowerCase().includes(searchText) || this.searchKeywords(file, searchText);
    });

    let tempMatrix = [];
    let matrixRows = Math.floor(tempArray.length / 8);

    for(let i = 0; i < matrixRows+1; i++){
      let tempRow = [];
      tempMatrix.push(tempRow);
    }

    for(let i = 0; i < tempMatrix.length; i++){
      for(let j = 0; j < 8; j++){
        if(tempArray[i * 8 + j] != null){
          tempMatrix[i][j] = tempArray[i * 8 + j];
        }
      }
    }
    this.files = tempMatrix;
  }

  /**
  * Searches for keywords of a file
  * @param file file which is being searched for keywords
  * @param searchText text which is compared to the keywords
  */
  searchKeywords(file, searchText){
    if(file.keywords == null){
      return false;
    }
    for(let i = 0; i < file.keywords.length; i++){
      if(file.keywords[i].toLocaleLowerCase().includes(searchText)){
        return true;
      }
    }
    return false;
  }

  /**
   * Stores the dragElement inside of the dropFolder
   * @param forward: determines if the file was moved to a child folder
   */
  async putInsideFolder(forward){
    if(this.dragElement != null && this.dropFolder != null){
      if(this.dragElement?.filename)
      {
        this.drive.moveTo(this.fileUploaded,this.dropFolder.id,this.dragElement.id);

        while(!this.fileUploaded.uploaded)
        {
          await new Promise(resolve => setTimeout(resolve, 100));
        }

        this.files = this.drive.deleteOneFromFiles(this.files,this.dragElement.id);
        if(!forward){
          this.userSearchFiles = this.drive.deleteOneFromFiles(this.userSearchFiles,this.dragElement.id);
        }
      }
      else if(this.dragElement?.foldername)
      {
        this.drive.moveTo(this.fileUploaded,this.dropFolder.id,0,this.dragElement.id);

        while(!this.fileUploaded.uploaded)
        {
          await new Promise(resolve => setTimeout(resolve, 100));
        }

        this.folders = this.drive.deleteOneFromFiles(this.folders,this.dragElement.id);

      }

      this.fileUploaded.uploaded = false;

      this.dragElement = null;
      this.dropFolder = null;
    }
  }

  /**
   * Event when a file or folder is starting to get dragged
   * @param event dragEvent
   * @param dragElement the dragged file or folder
   */
  dragStart(event: DragEvent, dragElement){
    this.dragElement = dragElement;
  }

  /**
   * Event when a file or folder is no longer being dragged
   * When activated over a folder putInsideFolder()-method is called
   */
  dragEnd(){
    if(this.draggedOverFolderElement != null && this.dropFolder != null){
      if(this.draggedOverFolderElement.classList.contains("draggedOver")){
        this.draggedOverFolderElement.classList.remove("draggedOver");
        this.putInsideFolder(true);
      }
      else{
        this.draggedOverFolderElement.classList.remove("draggedOverPath");
        this.putInsideFolder(false);
      }
      this.draggedOverFolderElement = null;
    }

  }

  /**
   * Event when a folder is entered while an element is getting dragged
   * @param event dragEvent
   * @param folder entered folder
   */
  dragEnter(event: DragEvent, folder){
    if((<Element>event.target).classList != null){
      if((<Element>event.target).classList.contains("fileContainer")){
        if(this.dragElement != folder){
          this.dropFolder = folder;
          this.draggedOverFolderElement = (<Element>event.target);
          this.draggedOverFolderElement.classList.add("draggedOver");
        }
      }
    }
  }

  /**
   * Event when a folder is no longer hovered while an element is getting dragged
   * @param event dragEvent
   */
  dragLeave(event: DragEvent){
    event.preventDefault();
    if(this.draggedOverFolderElement != null){
      this.draggedOverFolderElement.classList.remove("draggedOver");
      this.draggedOverFolderElement = null;
      this.dropFolder = null;
    }
  }

  /**
   * Event when a folder is hovered while an element is getting dragged
   * @param event dragEvent
   */
  allowDrop(event: DragEvent){
    event.preventDefault();
  }

}
