<?php

namespace App\Models;

use App\Facades\FirebaseFacade;
use ArrayAccess;
use Illuminate\Contracts\Auth\Authenticatable;
use JsonSerializable;
use Kreait\Firebase\Factory;

abstract class FirebaseModel implements ArrayAccess, JsonSerializable
{
    protected $connection = 'firebase';
    protected $collection;
    protected $attributes = [];
    protected $original = [];
    protected $exists = false;

    public function __construct(array $attributes = [])
    {
        $firebaseCredentiels= base64_decode(config('firebase.credentials'));
        $this->fill($attributes);
        $firebase = (new Factory)
        ->withServiceAccount(json_decode($firebaseCredentiels,true))
        ->withDatabaseUri(config('firebase.database_url'));
        // $this->collection = $firebase->createDatabase()->getReference($this->collection());
        $this->auth = $firebase->createAuth();
        $this->storage = $firebase->createStorage();
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }
    

    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getDirty()
    {
        $dirty = [];
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $this->original[$key] !== $value) {
                if (is_scalar($value) || is_null($value)) {
                    $dirty[$key] = $value;
                }
            }
        }
        return $dirty;
    }

    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public function save()
    {
        if (isset($this->attributes['photo']) && $this->attributes['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $photoUrl = $this->uploadPhoto($this->attributes['photo']);
            $this->attributes['photo'] = $photoUrl;
        }
    
        if (!$this->exists) {
            $this->attributes['id'] = FirebaseFacade::create($this->collection, $this->attributes);
            $this->exists = true;
        } else {
            FirebaseFacade::update($this->collection, $this->attributes['id'], $this->getDirty());
        }
        $this->syncOriginal();
        return true;
    }
    
    protected function uploadPhoto(\Illuminate\Http\UploadedFile $photo)
    {
        $fileName = time() . '_' . $photo->getClientOriginalName();
        $bucket = $this->storage->getBucket();
        $bucket->upload(
            $photo->get(),
            [
                'name' => 'users/' . $fileName,
            ]
        );
        return $bucket->object('users/' . $fileName)->signedUrl(new \DateTime('tomorrow'));
    }

    public static function find($id)
    {
        $data = FirebaseFacade::find((new static)->collection, $id);
        return $data ? new static($data + ['id' => $id]) : null;
    }

   
    public function toArray()
    {
        return $this->attributes;
    }

    public static function all()
    {
        $data = FirebaseFacade::all((new static)->collection);
        return collect($data)->map(function ($item, $key) {
            return new static($item + ['id' => $key]);
        })->values();
    }

    public function delete()
    {
        if ($this->exists) {
            FirebaseFacade::delete($this->collection, $this->attributes['id']);
            $this->exists = false;
        }
        return true;
    }
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    public function getKeyName()
    {
        return 'id';
    }

    public function syncOriginal()
    {
        $this->original = $this->attributes;
        return $this;
    }

    // ArrayAccess implementation
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->setAttribute($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    // JsonSerializable implementation
    public function jsonSerialize(): mixed
    {
        return $this->attributes;
    }

    // Magic method for accessing attributes as properties
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function getTable()
    {
        return $this->collection;
    }
    public function createWithAuthentication(array $userData): string
    {
        $userAuth = $this->auth->createUser([
            'email' => $userData['email'],
            'password' => $userData['password'],
        ]);
        $userData['auth_uid'] = $userAuth->uid;
        // $user = $this->userRepository->create($userData);
        return $userAuth->uid;
    }

}