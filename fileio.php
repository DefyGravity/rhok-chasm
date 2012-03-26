<?php

	class Chasm_File_IO
	{
		public static function streamFile( $srcFile, $dstFileName )
		{
			header( "Content-Disposition: attachment;filename=\"" 
				. $dstFileName . "\"");			
			header( "Content-Length: " . filesize( $srcFile ) );

			$src = fopen( $srcFile, "r" );
			$dst = fopen( "php://output", "w" );
			stream_copy_to_stream( $src, $dst );
		}
		
		public static function zipFiles( $dir, $files, $zipFileName )
		{
			$zip = new ZipArchive();
			$filename = $zipFileName;
			
			if ($zip->open($zipFileName, ZIPARCHIVE::CREATE)!==TRUE) {
			    die("cannot open <$zipFileName>\n");
			}
			
			for ( $fileIdx = 0; $fileIdx < count( $files ); $fileIdx++ )
			{
				$zip->addFile( $dir . $files[ $fileIdx ], $files[ $fileIdx ] );
			}

			$zip->close();
		}
	}
?>