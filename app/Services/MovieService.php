<?php

namespace App\Services;

use App\Interfaces\MovieRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MovieService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function getAllMovies($search = null)
    {
        return $this->movieRepository->getAllPaginated(6, $search);
    }

    public function getLatestMovies()
    {
        return $this->movieRepository->getLatestPaginated(10);
    }

    public function getMovieById($id)
    {
        return $this->movieRepository->findById($id);
    }

    public function createMovie(array $data, $file = null)
    {
        if ($file) {
            $data['foto_sampul'] = $file->store('movie_covers', 'public');
        }

        return $this->movieRepository->create($data);
    }

    public function updateMovie($id, array $data, $file = null)
    {
        $movie = $this->movieRepository->findById($id);

        if ($file) {
            $randomName = Str::uuid()->toString();
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = $randomName . '.' . $fileExtension;

            // Simpan foto ke folder public/images
            $file->move(public_path('images'), $fileName);

            // Hapus foto lama
            if (File::exists(public_path('images/' . $movie->foto_sampul))) {
                File::delete(public_path('images/' . $movie->foto_sampul));
            }

            $data['foto_sampul'] = $fileName;
        }

        return $this->movieRepository->update($id, $data);
    }

    public function deleteMovie($id)
    {
        $movie = $this->movieRepository->findById($id);

        // Hapus file foto terkait jika ada
        if (File::exists(public_path('images/' . $movie->foto_sampul))) {
            File::delete(public_path('images/' . $movie->foto_sampul));
        }

        return $this->movieRepository->delete($id);
    }
}
