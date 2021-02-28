import {Component, ElementRef, Input, ViewChild, ViewContainerRef, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {Router} from "@angular/router";
import {SharedService} from "../../../../Services/shared.service";

@Component({
  selector: 'upload',
  template: `
    <div #uploadModal class="modalWindowWrapper">
      <div class="modalWindow">
        <h1 i18n>Upload File</h1>
        <label i18n>File</label>
        <input class="fileUploader" name="fileUpload" type="file"  (change)="eventTarget = $event.target;verifyFile()">
        <label i18n>Keywords</label>
        <div #keywordContainer class="keywordEditContainer">
          <div *ngFor="let keyword of addedKeywords; index as i;" class="keywordEdit">
            {{keyword}}
            <i class="crossIcon keywordDeleteButton" (click)="removeKeyword(i)"></i>
          </div>
          <i #addKeywordButton class="plusIconBlue addKeywordButton" (click)="startKeywordInput()"></i>
        </div>
        <div class="keywordInputContainerWrapper">
          <div #keywordInputContainer class="keywordInputContainer">
            <input #keywordInput maxlength="25" class="keywordInput" type="text" name="addedKeyword" i18n-placeholder placeholder="Add Keyword...">
            <i class="plusIconBlue addKeywordButton" (click)="addKeyword()"></i>
          </div>

        </div>
        <div #errorMessage class="invisible" i18n>Keyword already exists!</div>
        <div #errorMessageCount class="invisible" i18n>Limited to 5 keywords!</div>
        <div #normalPlaceholder class="invisible" i18n>Add Keyword...</div>
        <div class="buttonVersioningWrapper">
          <button #submitBtn class="buttonSuccess" (click)="createFileSubmit()" disabled i18n>Upload</button>
          <button class="buttonCancel" (click)="cancel()" i18n>Cancel</button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .modalWindow
    {
      position: absolute;
      width: 25vw;
      background-color: white;
      left: 38%;
      top: 25%;
      display: flex;
      flex-direction: column;
      padding: 35px;
      border-radius: 15px;
      height: auto !important;
      color: #36506C;
    }

    .modalWindowWrapper
    {
      width: 100vw;
      position: fixed;
      height: 100vh;
      background-color: rgba(0,0,0,0.8);
      left: 0;
      top: 0;
      z-index: 1;
    }
    .fileUploader
    {
      height: 8vw;
      width: 100%;
      border: 3px solid #36506C;
      text-align-last: center;
      margin-bottom: 20px;
    }
    button
    {
      width: 32%;
      height: 2vw;
    }
    .buttonVersioningWrapper{
      display: flex;
      flex-direction: row;
      width: 100%;
      justify-content: space-evenly;
    }
    .keywordEditContainer {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      width: 90%;
      margin: 2% 0% 2% 0;
    }

    .keywordEdit {
      background-color: #36506C;
      color: white;
      border-radius: 10px;
      width: fit-content;
      margin: 1%;
      padding-left: 0.6vw;
      border: 2px solid #36506C;
      display: flex;
      align-items: center;
      justify-content: space-evenly;
      max-width: 100%;
      overflow: hidden;
    }

    .keywordDeleteButton {
      width: 1.5vw;
      height: 1.5vw;
      background-size: 50% !important;
      transition: 0.2s;
    }

    .keywordDeleteButton:hover {
      background-size: 60% !important;
      cursor: pointer;
    }

    .addKeywordButton {
      width: 1.5vw;
      height: 1.5vw;
      transition: 0.1s;
      border: 2px solid #36506C;
      border-radius: 50%;
      background-size: 60% !important;
      margin-left: 1%;
    }

    .addKeywordButton:hover {
      background-color: #36506C;
      background-image: url("./../assets/Images/Icons/PlusIcon_White.svg") !important;
      transform: scale(1.1);
      cursor: pointer;
    }

    .keywordInputContainerWrapper {
      width: 100%;
      height: 1.5vw;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 2%;
      margin-bottom: 4%;
    }

    .keywordInputContainer {
      display: none;
      height: 100%;
      width: 60%;
      justify-content: center;
      align-items: center;
    }

    .keywordInput {
      height: 100%;
      border: 1px solid #36506C;
      border-radius: 5px;
    }

    .errorMessage::placeholder {
      color: #980c12;
    }

    .invisible {
      display: none;
    }

    ::-webkit-file-upload-button {
      border: none;
      background-color: transparent;
      background-image: url("./../assets/Images/Icons/CloudIcon.svg");
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      height: 50%;
      width: 40%;
      margin-top: 2vw;
      font-size: 0;
    }

    @media (max-width: 1270px) {
      .modalWindow
      {
        width: 40vw;
        left: 30%;
      }
      button
      {
        height: 3vw;
      }

      .keywordDeleteButton {
        width: 3.4vw;
        height: 2.7vw;
      }

      .addKeywordButton {
        width: 2.7vw;
        height: 2.7vw;
      }

      .keywordInputContainerWrapper
      {
        height: 3vw;
      }
      .keywordInputContainer
      {
        width: 80%;
      }
    }

    @media (max-width: 800px) {
      .modalWindow {
        width: 65vw;
        left: 9%;
      }

      .fileUploader {
        height: 20vw;
      }

      button {
        width: 35%;
        height: 7vw;
      }

      #keyword_input
      {
        height: 4vw;
      }

      .keywordDeleteButton {
        width: 5.4vw;
        height: 5vw;
      }

      .addKeywordButton {
        width: 5.7vw;
        height: 5.7vw;
      }
      .keywordInputContainerWrapper {
        height: 5vw;
      }

      ::-webkit-file-upload-button {
        margin-top: 5vw;
        margin-left: -2vw;
      }
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class UploadfileComponent
{

  newFile: any;
  fileUploaded;
  eventTarget;
  addedKeywords = [];
  @ViewChild('submitBtn') subbtn: ElementRef;
  @ViewChild('uploadModal',{read: ViewContainerRef}) uploadModal;
  @ViewChild('keywordContainer') keywordContainer: ElementRef;
  @ViewChild('keywordInput') keywordInput: ElementRef;
  @ViewChild('addKeywordButton') addKeywordButton: ElementRef;
  @ViewChild('keywordInputContainer') keywordInputContainer: ElementRef;
  @ViewChild('errorMessage') errorMessage: ElementRef;
  @ViewChild('errorMessageCount') errorMessageCount: ElementRef;
  @ViewChild('normalPlaceholder') normalPlaceholder: ElementRef;



  constructor(private drive: DriveService,private router: Router,private shared: SharedService) {
    this.fileUploaded = {uploaded: false};
    this.newFile = this.newFile = {};
    if(this.shared.getObject("mousemenu") !== "NotFound")
     this.shared.getObject('mousemenu')?.hideWhenInterActionClicked();

  }

  /** verify if added content is a folder
   * triggered by onchange when file/filder will be added to input element
   */
  verifyFile()
  {
    if(this.eventTarget.files.length > 0)
    {
      this.subbtn.nativeElement.disabled = false;
      this.startKeywordInput();
    }
  }

  async createFileSubmit()
  {

    let files;

    let isInsideFolder = false;

    if(this.router.url === "/my")
      files = this.shared.getObject("mydocument").files;
    else if(this.router.url === "/department")
      files = this.shared.getObject("departmentdocument").files;
    else if(this.router.url === "/shared")
      return;

    if(this.router.url.includes('department/folder/') || this.router.url.includes('my/folder/'))
    {
      files = this.shared.getObject('currentFolderContent').files;
      isInsideFolder = true;
    }

    const file = this.eventTarget.files[this.eventTarget.files.length-1];


    // check here for file duplications

    // make 2d array 1d
    let tempArray = [];
    for(let i = 0; i < files.length; i++){
      for(let j = 0; j < files[i].length; j++){
        tempArray.push(files[i][j]);
      }
    }

    // check if a file with same name and extension has been inserted
    tempArray = tempArray.filter(currentFileToCheck => {
      // split to get rid of extension at end of filename
      return currentFileToCheck.filename === file.name.split('.')[0];
    });

    // if duplication occured then push new version
    if(tempArray.length > 0)
    {
      for(let i = 0; i < this.addedKeywords.length; i++){
        if(tempArray[0].keywords.find(element => element.toLowerCase() == this.addedKeywords[i].toLowerCase()) != undefined){
          this.addedKeywords.splice(i,1);
          i--;
        }
      }

      this.drive.addNewFileVersion(file,this.newFile,tempArray[0].id,tempArray[0].version_group_id,this.fileUploaded,this.addedKeywords)
      // wait till response from db with new created file
      while(!this.fileUploaded.uploaded)
      {
        await new Promise(resolve => setTimeout(resolve, 100));
      }
      this.fileUploaded.uploaded = false;

      // get the current Version and exchange it with the newly uploaded version
      for(let i = 0; i < files.length; i++){
        for(let j = 0; j < files[i].length; j++){
          if(files[i][j].id === tempArray[0].id)
          {
            files[i][j] = this.newFile[0];
          }
        }
      }
    }
    //otherwise make complete new file
    else
    {
      // POST new file into db
      this.drive.makeFile(file,this.fileUploaded,this.newFile,this.addedKeywords,isInsideFolder);

      // wait till response from db with new created file
      while(!this.fileUploaded.uploaded)
      {
        await new Promise(resolve => setTimeout(resolve, 100));
      }
      this.fileUploaded.uploaded = false;

      this.newFile[0].keywords = this.addedKeywords;
      // push file into list to make it visible in view
      if(files[files.length-1]?.length > 7)
      {
        files.push([]);
        files[files.length-1].push(this.newFile[0]);
      }
      else
      {
        if(!files[0]){
          files.push([]);
        }
        // check if its pushable ( array has a value at all)
        if(!files[files.length-1 < 0 ? 0 : files.length-1].push(this.newFile[0]))
        {
          //if not then push new index first
          files.push([]);
          files[files.length-1 < 0 ? 0 : files.length-1].push(this.newFile[0]);
        }
      }
    }

    this.eventTarget = undefined;
    this.cancel()
  }


  /**
   * shows the input element for the keyword input
   */
  startKeywordInput(){
    this.keywordInputContainer.nativeElement.style.display = 'flex';
    this.keywordContainer.nativeElement.removeChild(this.addKeywordButton.nativeElement);
  }

  /**
   * checks if the keyword is viable and adds it to the keyword list
   */
  addKeyword(){
    let keywordValue = this.keywordInput.nativeElement.value;

    if(keywordValue != ''){

      // checks if the keyword already exists for the file
      let found = this.addedKeywords.find(element => element.toLowerCase() == keywordValue.toLowerCase());
      // if there are already 5 keywords show error message
      if(this.addedKeywords.length >=5){
        this.keywordInput.nativeElement.placeholder = this.errorMessageCount.nativeElement.innerHTML;
        this.keywordInput.nativeElement.classList.toggle('errorMessage');
        this.keywordInput.nativeElement.value = '';
        setTimeout(()=>this.removeErrorMessage(), 3000);
      }
      else if(found == undefined){
        this.addedKeywords.push(keywordValue);
        this.keywordInput.nativeElement.value = '';
        this.keywordInputContainer.nativeElement.style.display = 'none';
        this.errorMessage.nativeElement.display = 'none';
        this.keywordContainer.nativeElement.appendChild(this.addKeywordButton.nativeElement);
      }
      // if it exists shows error message
      else{
        this.keywordInput.nativeElement.placeholder = this.errorMessage.nativeElement.innerHTML;
        this.keywordInput.nativeElement.classList.toggle('errorMessage');
        this.keywordInput.nativeElement.value = '';
        setTimeout(()=>this.removeErrorMessage(), 3000);
      }
    }
    // close the input container
    else{
      this.keywordInput.nativeElement.value = '';
      this.keywordInputContainer.nativeElement.style.display = 'none';
      this.errorMessage.nativeElement.display = 'none';
      this.keywordContainer.nativeElement.appendChild(this.addKeywordButton.nativeElement);
    }

  }

  /**
   * removes the error message from the input element
   */
  removeErrorMessage(){
    this.keywordInput.nativeElement.placeholder = this.normalPlaceholder.nativeElement.innerHTML;
    this.keywordInput.nativeElement.classList.toggle('errorMessage');
  }

  /**
   * removes the clicked keyword element from the view
   */
  removeKeyword(index){
    this.addedKeywords.splice(index,1);
  }

  /**
   * get out of version modal window without saving
   */
  cancel()
  {
    if(this.shared.getObject("mousemenu") !== "NotFound" && this.shared.getObject("input") !== "NotFound")
    {
      this.shared.getObject("mousemenu").modalWindowInitiated = false;
      this.shared.getObject("mousemenu").input.remove(0);
      this.shared.getObject("input").clear();
      this.shared.removeObject("input");
    }
    else{
      //make object destroy itself
      this.uploadModal
        .element
        .nativeElement
        .parentElement
        .removeChild(this.uploadModal.element.nativeElement);
    }
  }

}
