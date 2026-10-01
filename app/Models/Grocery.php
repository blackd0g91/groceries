<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grocery extends Model {

    use HasFactory;
    
    protected $fillable = [
        'name',
        'amount',
        'selected',
    ];

    public function tausteSearchLink() {
        return '<span><a href="' . config('market.tauste.search_link') . urlencode($this->name) . '" target="_blank" rel="noopener noreferrer">'.$this->name.'</a></span>';
    }

    public function tableRowUnselected($position) {

        $class = ($position % 2 == 0) ? 'even' : 'odd';

        $html = '<tr class="' . $class . '">';
        $html .= '<td>'. $this->tausteSearchLink() .'</td>';
        for ($i = 1; $i <= 5; $i++) {
            $html .= '<td><a href="/select/' . $this->id . '?value=' . $i . '" select-and-hide>' . $i . '</a></td>';
        }
        $html .= '</tr>';

        return $html;
    }

    public function tableRowSelected($position) {
        $class = ($position % 2 == 0) ? 'even' : 'odd';

        $html = '<tr class="' . $class . '">';
        $html .= '<td>'. $this->tausteSearchLink() .'</td>';
        for ($i = 1; $i <= 5; $i++) {
            if ($this->amount == $i) {
                $html .= '<td><p>' . $i . '</p></td>';
            } else {
                $html .= '<td><a href="/select/' . $this->id . '?value=' . $i . '">' . $i . '</a></td>';
            }
        }
        $html .= '<td><a class="trash" href="/trash/'. $this->id .'"><i><svg style="color:white; height:30px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></i></a></td>';
        $html .= '</tr>';
        

        return $html;
    }

}
