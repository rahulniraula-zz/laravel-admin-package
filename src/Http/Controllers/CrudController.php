<?php

namespace Geeklearners\Http\Controllers;

use App\Http\Controllers\Controller;
use Geeklearners\Exceptions\InvalidModelException;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Str;

class CrudController extends Controller
{
    private $modelUrlSegment;
    private $model;
    private $modelPath;
    public function __construct()
    {
        $this->modelUrlSegment = app('request')->segment(2);
        $this->model = str_replace(' ', '', ucwords(str_replace('-', ' ', ($this->modelUrlSegment))));
        $this->modelPath = $this->getModel();
    }

    protected function getModel()
    {
        $classes = config('admin.crud_classes');
        $d = preg_grep('/' . $this->model . '$/', $classes);
        if (count($d) > 0) {
            return $d[0];
        } else {
            throw new InvalidModelException("No Model Configured in admin.php configuration file");
        }
    }
    public function index()
    {
        if (property_exists($this->modelPath, 'form_requests') && isset($this->modelPath::$form_requests['index'])) {
            app()->make($this->modelPath::$form_requests['index']);
        }
        $items = $this->modelPath::paginate();
        $cols = [];
        $cols_to_include = method_exists($this->modelPath, 'colsToInclude') ? call_user_func([$this->modelPath, 'colsToInclude']) : [];
        if (count($items) > 0) {
            $c = $this->filterColsName($cols_to_include);
            $cols = array_intersect(array_keys($items[0]->toArray()), $c);
        }
        $additional_cols = method_exists($this->modelPath, 'additionalColumns') ? call_user_func([$this->modelPath, 'additionalColumns']) : [];
        $a_cols = $this->filterColsName($additional_cols);
        // dd($a_cols);
        return view("admin::index")->with(['items' => $items, 'cols' => $cols, 'a_cols' => $a_cols]);
    }

    private function filterColsName($cols)
    {
        // dd($cols);
        $c = [];
        foreach ($cols as $key => $val) {
            if (is_callable($val)) {
                $c[] = $key;
            } else {
                $c[] = $val;
            }
        }
        return $c;
    }
    public function create()
    {
        $fields = $this->modelPath::$form_fields;
        return view("admin::create")
            ->with('modelUrlSegment', $this->modelUrlSegment)
            ->with('class_name', $this->modelPath)
            ->with('fields', $fields);
    }

    public function store()
    {
        if (property_exists($this->modelPath, 'form_requests') && isset($this->modelPath::$form_requests['store'])) {
            app()->make($this->modelPath::$form_requests['store']);
        }
        $data = $this->modelPath::create(app('request')->all());
        session()->flash('flash_success', 'Resource created successful!');
        return redirect()
            ->route(config('admin.prefix') . '.index', ['model' => $this->modelUrlSegment]);
    }

    public function edit($model, $id)
    {
        $item = $this->modelPath::find($id);
        $fields = $this->modelPath::$form_fields;
        if (property_exists($this->modelPath, 'form_requests') && isset($this->modelPath::$form_requests['edit'])) {
            app()->make($this->modelPath::$form_requests['edit']);
        }
        return view("admin::edit")->with('class_name', $this->modelPath)
            ->with('modelUrlSegment', $this->modelUrlSegment)
            ->with('fields', $fields)->with('item', $item);
    }
    public function update($model, $id)
    {
        if (property_exists($this->modelPath, 'form_requests') && isset($this->modelPath::$form_requests['update'])) {
            app()->make($this->modelPath::$form_requests['update']);
        }
        $item = $this->modelPath::find($id);
        $item->update(app('request')->all());
        return redirect()
            ->route(config('admin.prefix') . '.index', ['model' => $model])
            ->with('flash_success', 'Resource updated successful!');
    }

    public function destroy($model, $id)
    {
        if (property_exists($this->modelPath, 'form_requests') && isset($this->modelPath::$form_requests['delete'])) {
            app()->make($this->modelPath::$form_requests['delete']);
        }
        $this->modelPath::destroy($id);
        return redirect()
            ->route(config('admin.prefix') . '.index', ['model' => $model])
            ->with('flash_success', 'Resource deleted successful!');
    }
}
