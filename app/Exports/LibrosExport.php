<?php

namespace App\Exports;

use App\Libro;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibrosExport implements FromCollection,WithHeadings{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            // 'id',
            'isbn',
            'titulo',
            'editorial',
            'piezas'
        ];
    }
    public function collection(){
         $libros = DB::table('libros')
                        ->select('isbn', 'titulo', 'editorial', 'piezas')
                        ->orderBy('editorial','asc')->get();
        return $libros;
        
    }
}
