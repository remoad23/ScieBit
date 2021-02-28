import {Component, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {SharedService} from "../../../../Services/shared.service";
import {Router} from "@angular/router";

@Component({
  selector: 'shareddocument',
  template: `
    <router-outlet></router-outlet>
    <div class="droplistGroupContainer" id="folderContainer">
      <div class="droplistFolder" *ngFor="let folder of folders; let k = index;">
        <folder (dblclick)="goToNextFolder(folder_detail.id)"
                *ngFor="let folder_detail of folder; index as i" [hashName]="folder_detail.folder" [folderID]="folder_detail.id">
          {{folder_detail.foldername}}
        </folder>
      </div>
    </div>

    <div class="droplistGroupContainer">
      <div class="droplistMydocument" *ngFor="let file of files; let k = index;">
        <file *ngFor="let file_details of file; index as i"
              [fileID]="file_details.id" [hashName]="file_details.file" [version_group_id]="file_details.version_group_id" [fileDetails]="file_details">
          {{file_details.filename}}
        </file>
      </div>
    </div>`
  ,
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
    shareddocument
    {
      width: 100%;
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

    @media (max-width: 1270px) {
      .droplistMydocument {
        flex-wrap: wrap;
        margin-bottom: 0;
      }

      .droplistFolder {
        flex-wrap: wrap;
        margin-bottom: 0;
      }

    }

    @media (max-width: 600px) {
      .droplistMydocument {
        flex-wrap: nowrap;
        margin-bottom: 2%;
        flex-direction: column;
      }
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class ShareddocumentComponent {

  files: any;
  folders: any;
  loadedFiles: any;
  userSearchFiles: any;
  drive: DriveService;
  currentSearchText = '';


  constructor(private _drive: DriveService,private shared: SharedService,private router: Router)
  {
    this.files = [];
    this.folders = [];
    this.loadedFiles = [];
    this.userSearchFiles = [];
    this.drive = _drive;
    this.drive.getDataByHttp("shareddocument",this.loadedFiles);
    this.drive.getDataByHttp("shareddocumentfolder",this.folders);
    this.drive.getDataByHttp("shareddocumentfiles",this.userSearchFiles);
    shared.insertObject("shareddocument",this);
    this.files = this.loadedFiles;
  }

  goToNextFolder(id)
  {
    this.shared.getObject("app").fileOrFolderSelected = false;
    this.shared.removeObject("selectedFile");
    this.shared.removeObject("selectedFolder");
    if(this.router.url === "/shared")
    {
      this.router.navigate([ "shared/folder/"+ id]);

    }
    else{
      this.router.navigate(['shared']).then(() =>{
        this.router.navigate([ "shared/folder/" + id],{ replaceUrl: true });

      });
    }

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

}
