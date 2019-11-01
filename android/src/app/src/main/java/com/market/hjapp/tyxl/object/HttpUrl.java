package com.market.hjapp.tyxl.object;

import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;

public class HttpUrl {

	// 公网
	// public static String httpStr = "http://121.52.211.163/kxdb/";
	public static String httpStr = "http://www.hjapk.com:8080/kxdbsvr/";

	/**
	 * POST
	 * 
	 * @param path
	 * @param params
	 * @return
	 */
	public static String setPostUrl(String url, String Str) {
		String result = null;
		try {
			// 第一步，创建HttpPost对象
			HttpPost request = new HttpPost(url);
			// 设置HTTP POST请求参数必须用NameValuePair对象
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			params.add(new BasicNameValuePair("json", Str));
			// 设置httpPost请求参数
			request.setEntity(new UrlEncodedFormEntity(params, HTTP.UTF_8));
			// 第二步，使用execute方法发�?HTTP GET请求，并返回HttpResponse对象
			HttpResponse httpResponse = new DefaultHttpClient()
					.execute(request);
			if (httpResponse.getStatusLine().getStatusCode() == 200) {
				// 第三步，使用getEntity方法活得返回结果
				result = EntityUtils.toString(httpResponse.getEntity());
			}
		} catch (Exception e) {
			return null;
		}
		return result;
	}

	/**
	 * GET
	 * 
	 * @param url
	 */
	public static String setGetUrl(String url) {
		String out = null;
		try {
			HttpClient client = new DefaultHttpClient();
			HttpPost request;
			request = new HttpPost(new URI(url));
			HttpResponse response = client.execute(request);

			//
			if (response.getStatusLine().getStatusCode() == 200) {
				HttpEntity entity = response.getEntity();
				if (entity != null) {
					out = EntityUtils.toString(entity);
				}
			}
		} catch (Exception e) {
			return null;
		}
		return out;
	}

	/**
	 * 获取网络图片资源
	 * @param url
	 * @return
	 */
	public static Bitmap returnBitMap(String url) {
		URL myFileUrl = null;
		Bitmap bitmap = null;
		try {
			myFileUrl = new URL(url);
		} catch (MalformedURLException e) {
			e.printStackTrace();
		}
		try {
			HttpURLConnection conn = (HttpURLConnection) myFileUrl
					.openConnection();
			conn.setDoInput(true);
			conn.connect();
			InputStream is = conn.getInputStream();
			bitmap = BitmapFactory.decodeStream(is);
			is.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return bitmap;
	}
}
