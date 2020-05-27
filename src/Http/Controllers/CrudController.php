<?php

namespace Geeklearners\Http\Controllers;

use App\Http\Controllers\Controller;
use Geeklearners\Exceptions\FieldsNotDeclaredException;
use Geeklearners\Exceptions\InvalidModelException;
use Illuminate\Http\UploadedFile;
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
        return view("admin::index")
            ->with(['items' => $items, 'cols' => $cols, 'a_cols' => $a_cols])->with('class_name', $this->modelPath);
    }

    private function filterColsName($cols)
    {
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
        if (method_exists($this->modelPath, 'formFields')) {
            $fields = $this->modelPath::formFields();
            return view("admin::create")
                ->with('modelUrlSegment', $this->modelUrlSegment)
                ->with('class_name', $this->modelPath)
                ->with('fields', $fields);
        } else {
            throw new FieldsNotDeclaredException("Please declare static 'formFields' method in $this->modelPath ");
        }
    }

    public function store()
    {
        if (property_exists($this->modelPath, 'form_requests') && isset($this->modelPath::$form_requests['store'])) {
            app()->make($this->modelPath::$form_requests['store']);
        }
        $data = $this->modelPath::create(app('request')->except($this->extractFileFields()));
        $this->processUploadFiles($data);
        session()->flash('flash_success', 'Resource created successful!');
        return redirect()
            ->route(config('admin.prefix') . '.index', ['model' => $this->modelUrlSegment]);
    }
    public function processUploadFiles($model)
    {
        $request = app('request');
        foreach ($this->extractFileFields() as $name) {
            $files = $request->file($name);
            $combined_names = [];
            if ($files) {
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $file_name = Str::random(16) . '.' . $file->getClientOriginalExtension();
                        $file->move(storage_path('app/' . $this->modelUrlSegment), $file_name);
                        $combined_names[] = $file_name;
                        $model->update([$name => implode('|', $combined_names)]);
                    }
                }
            }
        }
    }
    /**
     * Extract file fields from the model
     */
    private function extractFileFields()
    {
        $form_fields = $this->modelPath::formFields();
        $file_fields = [];
        array_filter($form_fields, function ($metadata, $field) use (&$file_fields) {
            if ($metadata['type'] == "file") {
                $file_fields[] = $field;
            }
        }, ARRAY_FILTER_USE_BOTH);
        return $file_fields;
    }

    public function edit($model, $id)
    {
        $item = $this->modelPath::find($id);
        $fields = $this->modelPath::formFields();
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
        $item->update(app('request')->except($this->extractFileFields()));
        $this->processUploadFiles($item);
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
