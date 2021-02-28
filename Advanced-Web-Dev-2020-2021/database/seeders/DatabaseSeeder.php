<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\{User, Admin, Folder};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->addParentFolderForeignKey();

        $this->call([DepartmentTableSeeder::class]);
        $this->call([AdminTableSeeder::class,UserTableSeeder::class]);

        User::factory(50)->create();
        Admin::factory(50)->create();

        $this->call([FolderTableSeeder::class,
            FileTableSeeder::class,
            KeywordTableSeeder::class,
            DepartmentuserTableSeeder::class,
            DepartmentadminTableSeeder::class,
            SharedfileTableSeeder::class]);

        Folder::factory()->insertRandomFolders();

        $this->call([SharedfolderTableSeeder::class]);
    }

    /**
     * Used to make the order of migration not disturb with the creating of the foreign key,
     * which is responsible for the parentfolder of  a file,sharedfile,folder or sharedfolder
     */
    private function addParentFolderForeignKey()
    {
        Schema::table('file', function($table)
        {
            $table->foreignId('parentfolder_id')->nullable()->constrained('folder')->onDelete('cascade');
        });

        Schema::table('sharedfile', function($table)
        {
            $table->foreignId('parentfolder_id')->nullable()->constrained('folder')->onDelete('cascade');
        });

        Schema::table('folder', function($table)
        {
            $table->foreignId('parentfolder_id')->nullable()->constrained('folder')->onDelete('cascade');
        });

        Schema::table('sharedfolder', function($table)
        {
            $table->foreignId('parentfolder_id')->nullable()->constrained('folder')->onDelete('cascade');
        });
    }


}
