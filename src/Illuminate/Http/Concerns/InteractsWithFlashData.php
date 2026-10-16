<?php

namespace Illuminate\Http\Concerns;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait InteractsWithFlashData
{
    /**
     * Retrieve an old input item.
     *
     * @param  string|null  $key
     * @param  \Illuminate\Database\Eloquent\Model|string|array|null  $default
     * @return string|array|null
     */
    public function old($key = null, $default = null)
    {
        $default = $default instanceof Model ? $default->getAttribute($key) : $default;

        return $this->hasSession() ? $this->session()->getOldInput($key, $default) : $default;
    }

    /**
     * Flash the input for the current request to the session.
     *
     * @return void
     */
    public function flash()
    {
        $this->session()->flashInput($this->input());
    }

    /**
     * Flash only some of the input to the session.
     *
     * @param  mixed  $keys
     * @return void
     */
    public function flashOnly($keys)
    {
        $this->session()->flashInput($this->removeFilesFromInput(
            $this->only(is_array($keys) ? $keys : func_get_args())
        ));
    }

    /**
     * Flash only some of the input to the session.
     *
     * @param  mixed  $keys
     * @return void
     */
    public function flashExcept($keys)
    {
        $this->session()->flashInput($this->removeFilesFromInput(
            $this->except(is_array($keys) ? $keys : func_get_args())
        ));
    }

    /**
     * Remove all uploaded files from the given input array.
     *
     * @param  array  $input
     * @return array
     */
    protected function removeFilesFromInput(array $input)
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->removeFilesFromInput($value);
            }

            if ($value instanceof UploadedFile) {
                unset($input[$key]);
            }
        }

        return $input;
    }

    /**
     * Flush all of the old input from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->session()->flashInput([]);
    }
}
