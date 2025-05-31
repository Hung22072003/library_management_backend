<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    use APIResponse;
    private $statisticsService;


    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $top6 = $this->statisticsService->getTotalAmountSuccess();
        return $this->responseSuccessWithData($top6);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getTotalQuantityBooks()
    {
        $total = $this->statisticsService->getTotalQuantityBooks();
        return $this->responseSuccessWithData($total);
    }

    public function getTop6MostBorrowedBooks()
    {
        $top6 = $this->statisticsService->getTop6MostBorrowedBooks();
        return $this->responseSuccessWithData($top6);
    }

    public function getTop6UsersBorrowMost()
    {
        $top6 = $this->statisticsService->getTop6UsersBorrowMost();
        return $this->responseSuccessWithData($top6);
    }
    public function getBorrowedBooksEachDayLast10Days()
    {
        $top6 = $this->statisticsService->getBorrowedBooksEachDayLast10Days();
        return $this->responseSuccessWithData($top6);
    }
    public function getReturnedVsReturnedLateRatio()
    {
        $top6 = $this->statisticsService->getReturnedVsReturnedLateRatio();
        return $this->responseSuccessWithData($top6);
    }
    public function getTotalBooksByCategory()
    {
        $top6 = $this->statisticsService->getTotalBooksByCategory();
        return $this->responseSuccessWithData($top6);
    }
    public function getTotalAmountSuccess()
    {
        $top6 = $this->statisticsService->getTotalAmountSuccess();
        return $this->responseSuccessWithData($top6);
    }
}
