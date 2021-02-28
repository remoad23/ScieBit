import {
  Component, ElementRef, HostListener, OnDestroy,
  ViewEncapsulation
} from '@angular/core';
import {SharedService} from "../../../../Services/shared.service";
import {DriveService} from "../../../../Services/drive.service";
import {ActivatedRoute, Router} from "@angular/router";
import {Location} from '@angular/common';

@Component({
  selector: 'foldercontent',
  template: `
    <div *ngIf="visible" class="folderContentWrapper">
      <div class="droplistGroupContainer" id="folderContainer">
        <div class="droplistFolder" *ngFor="let folder of folders; let k = index;">
          <ng-container *ngIf="!router.url.startsWith('/shared')">
            <folder draggable="true" (dragend)="dragEnd()" (dragstart)="dragStart($event,folder_detail)" (dragenter)="dragEnter($event, folder_detail)"
                    (dragleave)="dragLeave($event)" (dragover)="allowDrop($event)" (dblclick)="goToNextFolder(folder_detail.id)"
                    *ngFor="let folder_detail of folder; index as i" [folderID]="folder_detail.id" [hashName]="folder_detail.folder">
              {{ folder_detail.foldername }}
            </folder>
          </ng-container>
          <ng-container *ngIf="router.url.startsWith('/shared')">
            <folder (dblclick)="goToNextFolder(folder_detail.id)"
                    *ngFor="let folder_detail of folder; index as i" [folderID]="folder_detail.id" [hashName]="folder_detail.folder">
              {{ folder_detail.foldername }}
            </folder>
          </ng-container>
        </div>
      </div>

      <div class="droplistGroupContainer">
        <div class="droplistMydocument" *ngFor="let file of files; let k = index;">
          <ng-container *ngIf="!router.url.startsWith('/shared')">
            <file draggable="true" (dragend)="dragEnd()" (dragstart)="dragStart($event,file_details)" *ngFor="let file_details of file; index as i"
                  [fileID]="file_details.id" [hashName]="file_details.file" [fileDetails]="file_details">
              {{file_details.filename}}
            </file>
          </ng-container>
          <ng-container *ngIf="router.url.startsWith('/shared')">
            <file *ngFor="let file_details of file; index as i"
                  [fileID]="file_details.id" [hashName]="file_details.file" [fileDetails]="file_details">
              {{file_details.filename}}
            </file>
          </ng-container>
        </div>
      </div>
    </div>
  `,
  styles: [`
    foldercontent{
      width: 100%;
    }
    .folderContentWrapper{
      height: 100%;
      width: 100%;
    }
    .fileContainer{
      height: 100%;
      width: 100%;
    }
    .draggedOver
    {
      transform: scale(1.5);
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class FoldercontentComponent  {

  files: any;
  folders: any;
  loadedFiles: any;
  userSearchFiles: any;
  currentSearchText = '';
  id: number;
  visible: boolean;
  dragElement: any;
  dropFolder: any;
  draggedOverFolderElement: any;
  fileUploaded;

  constructor(private shared: SharedService,private drive: DriveService,private route: ActivatedRoute,private router: Router,private location: Location) {
    this.files = [];
    this.loadedFiles = [];
    this.userSearchFiles = [];
    this.folders = [];
    this.visible = true;

    //get Folder ID and then get folder data
    this.route.params.subscribe(params => {
      this.drive.getChildData( "file",this.loadedFiles,parseInt(params['id'] ) );
      this.drive.getChildData( "allfiles",this.userSearchFiles,parseInt(params['id'] ) );
      this.drive.getChildData( "folder",this.folders,parseInt(params['id'] ) );
      this.id = params['id'];
    });
    this.files = this.loadedFiles;
    this.shared.removeObject("currentFolderContent");
    this.shared.insertObject("currentFolderContent",this);
    this.fileUploaded = {uploaded: false};
  }

  goToNextFolder(id)
  {
    this.shared.getObject("app").fileOrFolderSelected = false;
    this.shared.removeObject("selectedFile");
    this.shared.removeObject("selectedFolder");
    if(this.router.url.includes('my/'))
    {
      this.router.navigate(['my']).then(() =>{
        this.router.navigate([ "my/folder/" + id],{ replaceUrl: true });
      });
    }
    else if(this.router.url.includes('shared/'))
    {
      this.router.navigate(['shared']).then(() =>{
        this.router.navigate([ "shared/folder/" + id],{ replaceUrl: true });
      });
    }
    else if(this.router.url.includes('department/'))
    {
      this.router.navigate(['department']).then(() =>{
        this.router.navigate([ "department/folder/" + id],{ replaceUrl: true });
      });
    }
  }

  goToPreviousFolder()
  {
    this.router.navigate(['my']).then(() =>{
      this.router.navigate([ "my/folder/" + 9],{ replaceUrl: true });
    });
  }

  /**
   * sorts the files array by name
   */
  sortByName(){
    this.folders = this.drive.sortByNameFolders(this.folders);
    this.files = this.drive.sortByNameFiles(this.files);
  }

  /**
   * sorts the files array by date
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

  @HostListener('window:popstate', ['$event'])
  onPopState(event) {
    this.shared.getObject("app").fileOrFolderSelected = false;
    this.shared.removeObject("selectedFile");
    this.shared.removeObject("selectedFolder");
    this.router.navigate(['my'],{ replaceUrl: true })
  }

}
