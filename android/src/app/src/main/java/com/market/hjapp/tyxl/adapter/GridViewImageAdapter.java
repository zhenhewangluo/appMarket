package com.market.hjapp.tyxl.adapter;

import java.util.List;

import android.app.Activity;
import android.graphics.drawable.Drawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.TextView;


import com.market.hjapp.R;
import com.market.hjapp.tyxl.AsyncImageLoader;
import com.market.hjapp.tyxl.ViewCache;
import com.market.hjapp.tyxl.AsyncImageLoader.ImageCallback;
import com.market.hjapp.tyxl.object.ImageAndText;
public class GridViewImageAdapter extends ArrayAdapter<ImageAndText> {

	private LayoutInflater mInflater;
	private GridView gridview;
	private AsyncImageLoader asyncImageLoader;

	public GridViewImageAdapter(Activity activity, List<ImageAndText> itemList,
			GridView gridview) {
		super(activity, 0, itemList);
		this.gridview = gridview;
		asyncImageLoader = new AsyncImageLoader();
	}

	public View getView(int position, View convertView, ViewGroup parent) {
		
		Activity activity = (Activity) getContext();
		View rowView = convertView;
		ViewCache viewCache;

		if (convertView == null) {
			LayoutInflater inflater = activity.getLayoutInflater();
			rowView = inflater.inflate(R.layout.gridview_item, null);
			viewCache = new ViewCache(rowView);
			rowView.setTag(viewCache);
		} else {
			viewCache = (ViewCache) convertView.getTag();
		}

		ImageAndText imageAndText =getItem(position);
		// Load the image and set it on the ImageView
		String imageUrl = imageAndText.getImageUrl();
		ImageView imageView = viewCache.getImageView();
		imageView.setTag(imageUrl);
		Drawable cachedImage = asyncImageLoader.loadDrawable(imageUrl,
				new ImageCallback() {
					public void imageLoaded(Drawable imageDrawable,
							String imageUrl) {
						ImageView imageViewByTag = (ImageView) gridview
								.findViewWithTag(imageUrl);
						if (imageViewByTag != null) {
							imageViewByTag.setImageDrawable(imageDrawable);
						}
					}
				});
		if (cachedImage == null) {
			imageView.setImageResource(R.drawable.default_image);
		} else {
			imageView.setImageDrawable(cachedImage);
		}
		// Set the text on the TextView
		TextView textView1 = viewCache.getOldNumView();
		textView1.setText("已兑：" + imageAndText.getOldnum());
		TextView textView2 = viewCache.getNewNumView();
		textView2.setText("剩余：" + imageAndText.getNewnum());
		rowView.setLayoutParams(new GridView.LayoutParams(125, 125));
		return rowView;
	}

}