import click
import requests
import zipfile
import os
import json
import shutil
from tqdm import tqdm


@click.group()
def cli():
    pass


@cli.command()
@click.option('--bundlename', prompt='Please enter your name/package', help='Your package name in format name/package.')
@click.option('--path', help='Absolute path to install packages. Defaults to currentDir/Assets/vendor.')
@click.option('--version', help='Version number for package.')
def install(bundlename, path, version):
    url = "http://snatch-it.org/" + bundlename + "/download"
    if version is not None:
        url += "/" + version

    download(bundlename, url, path)


@cli.command()
@click.option('--path', help='Absolute path to locate info.json. Defaults to currentDir/Assets/vendor.')
def pull(path):
    if path is None:
        path = os.path.join(os.getcwd(), "Assets")
        path = os.path.join(path, "vendor")
        path += os.sep

    with open(path + "snatch.json") as snatch_file:
        snatch_info = json.load(snatch_file)
        for bundle, version in snatch_info["assets"].items():
            download(bundle, "http://snatch-it.org/" + bundle + "/download/" + version, path)
        for bundle, version in snatch_info["packages"].items():
            download(bundle, "http://snatch-it.org/" + bundle + "/download/" + version, path)


# installs unzipped package to the given directory
def download(bundlename, url, path):
    print("Downloading " + bundlename + "!")
    returned_request = requests.get(url, stream=True)
    total_size = int(returned_request.headers.get('content-length', 0))/(32*1024)

    with open('output.bin', 'wb') as f:
        for data in tqdm(returned_request.iter_content(32 * 1024), total=total_size, unit='B', unit_scale=True):
            f.write(data)

    # make sure web response is good before continuing
    if returned_request.status_code != 200:
        print("Bad response for url: %s" % url)
        return

    # make sure we have a zip file
    if not zipfile.is_zipfile("output.bin"):
        print("Returned file is not a zip at url: %s" % url)
        return

    print("Successfully downloaded " + bundlename + "!")

    # create a zipfile object
    zip_file = zipfile.ZipFile("output.bin")

    # set extract path
    if path is None:
        path = os.path.join(os.getcwd(), "Assets")
        if not os.path.exists(path):
            os.makedirs(path)
        path = os.path.join(path, "vendor")
        if not os.path.exists(path):
            os.makedirs(path)
        path += os.sep

    # get root snatch info.json if it exists, else create one
    snatch_path = os.path.join(path, "snatch.json")
    snatch_json = {"assets": {}, "packages": {}}
    if os.path.isfile(snatch_path):
        data_file = open(snatch_path, 'r')
        snatch_json = json.load(data_file)
    snatch_assets = snatch_json["assets"]
    snatch_packages = snatch_json["packages"]

    downloading_path = os.path.join(path, "downloading")
    zip_file.extractall(downloading_path)

    # now let's unzip all the inner zips
    for filename in os.listdir(downloading_path):
        new_path = os.path.join(downloading_path, filename)
        if zipfile.is_zipfile(new_path):
            print("Unzipping " + filename[:-4] + " from " + bundlename + "!")
            inner_zip_file = zipfile.ZipFile(new_path)
            inner_zip_file.extractall(path)

            # check to see if there are any zips in vendor
            for inner_filename in os.listdir(path):
                new_inner_path = os.path.join(path, inner_filename)
                if zipfile.is_zipfile(new_inner_path):
                    inner_zip_file = zipfile.ZipFile(new_inner_path)
                    inner_zip_file.extractall(path)
                    os.remove(new_inner_path)

        # let's add this bundle's assets to our info.json
        elif filename == "info.json":
            inner_data_file = open(new_path, 'r')
            inner_info_json = json.load(inner_data_file)
            for asset in inner_info_json["assets"]:
                snatch_assets[asset["bundlename"]] = asset["version"]
            if "package" in inner_info_json:
                for package in inner_info_json["package"]:
                    snatch_packages[package["bundlename"]] = package["version"]

    # delete the downloading folder and output.bin
    os.remove("output.bin")
    os.remove(os.path.join(path, "info.json"))
    shutil.rmtree(downloading_path)

    # dump json
    with open(snatch_path, 'w+') as out_file:
        json.dump(snatch_json, out_file, indent=4, sort_keys=True)

    print("Successfully installed " + bundlename + "!")


if __name__ == '__main__':
    cli()