package com.market.hjapp.service;

interface IAppServiceInterface {
	void downloadApp(int appDbId, String url, String fileName, String downloadId, String appName, String pid);
    int getDownloadProgress(int appDbId);
    int getDownloadStatus(int appDbId);
}